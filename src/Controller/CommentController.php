<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AuthCore;
use App\Core\CsrfCore;
use App\Core\DatabaseCore;
use App\Core\JsonCore;
use App\Model\CommentModel;
use App\Model\ImageModel;
use App\Model\UserModel;
use App\Service\MailerService;
use Throwable;

class CommentController
{
	private const CONTENT_MAX_LENGTH = 500;

	public function create(): void
	{
		if (!AuthCore::check()) {
			JsonCore::error(401, "Sign in required.");
		}

		$submittedToken = is_string($_POST[CsrfCore::fieldName()] ?? null)
			? $_POST[CsrfCore::fieldName()]
			: "";
		if (!CsrfCore::validate($submittedToken)) {
			JsonCore::error(403, "Invalid CSRF token.");
		}

		$imageId = (int) ($_POST["image_id"] ?? 0);
		$content = trim((string) ($_POST["content"] ?? ""));

		if ($imageId <= 0) {
			JsonCore::error(400, "Missing image.");
		}
		if ($content === "") {
			JsonCore::error(400, "Comment cannot be empty.");
		}
		if (mb_strlen($content) > self::CONTENT_MAX_LENGTH) {
			JsonCore::error(400, "Comment is too long (max " . self::CONTENT_MAX_LENGTH . " characters).");
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new DatabaseCore($dbConfig))->connection();

		$images = new ImageModel($pdo);
		$image  = $images->findById($imageId);
		if ($image === null) {
			JsonCore::error(404, "Image not found.");
		}

		$commenterId = (int) AuthCore::id();
		$comments    = new CommentModel($pdo);
		$commentId   = $comments->create($commenterId, $imageId, $content);

		$row = $comments->findByIdEnriched($commentId);
		if ($row === null) {
			JsonCore::error(500, "Could not load created comment.");
		}

		$this->maybeSendNotification($pdo, (int) $image["user_id"], $commenterId, $imageId, $content);

		$createdTs    = strtotime((string) $row["created_at"]);
		$createdIso   = $createdTs !== false ? date("c", $createdTs) : "";
		$createdHuman = $createdTs !== false ? date("M j, H:i", $createdTs) : "";

		JsonCore::success([
			"comment" => [
				"id"            => (int) $row["id"],
				"image_id"      => (int) $row["image_id"],
				"user_id"       => (int) $row["user_id"],
				"username"      => (string) $row["username"],
				"avatar_path"   => isset($row["avatar_path"]) ? (string) $row["avatar_path"] : "",
				"content"       => (string) $row["content"],
				"created_at"    => (string) $row["created_at"],
				"created_iso"   => $createdIso,
				"created_human" => $createdHuman,
			],
			"comment_count" => $comments->countByImageId($imageId),
			"is_mine"       => true,
		]);
	}

	private function maybeSendNotification(
		\PDO $pdo,
		int $authorId,
		int $commenterId,
		int $imageId,
		string $content
	): void {
		if ($authorId === $commenterId) {
			return;
		}

		$users  = new UserModel($pdo);
		$author = $users->findById($authorId);
		if ($author === null) {
			return;
		}
		if ((int) ($author["notify_comments"] ?? 0) !== 1) {
			return;
		}

		$commenter         = $users->findById($commenterId);
		$commenterUsername = is_array($commenter) ? (string) $commenter["username"] : "Someone";

		try {
			$mailConfig = require __DIR__ . "/../../config/mail.php";
			(new MailerService($mailConfig))->sendCommentNotification(
				(string) $author["email"],
				(string) $author["username"],
				$commenterUsername,
				$content,
				$imageId,
			);
		} catch (Throwable $error) {
			error_log("[comments] notification email failed: " . $error->getMessage());
		}
	}

	public function delete(): void
	{
		if (!AuthCore::check()) {
			JsonCore::error(401, "Sign in required.");
		}

		$submittedToken = is_string($_POST[CsrfCore::fieldName()] ?? null)
			? $_POST[CsrfCore::fieldName()]
			: "";
		if (!CsrfCore::validate($submittedToken)) {
			JsonCore::error(403, "Invalid CSRF token.");
		}

		$commentId = (int) ($_POST["comment_id"] ?? 0);
		if ($commentId <= 0) {
			JsonCore::error(400, "Missing comment.");
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new DatabaseCore($dbConfig))->connection();

		$comments = new CommentModel($pdo);
		$comment  = $comments->findById($commentId);
		if ($comment === null) {
			JsonCore::error(404, "Comment not found.");
		}

		if ((int) $comment["user_id"] !== (int) AuthCore::id()) {
			JsonCore::error(403, "You can only delete your own comments.");
		}

		if (!$comments->delete($commentId)) {
			JsonCore::error(500, "Could not delete comment.");
		}

		$imageId = (int) $comment["image_id"];

		JsonCore::success([
			"comment_id"    => $commentId,
			"image_id"      => $imageId,
			"comment_count" => $comments->countByImageId($imageId),
		]);
	}

}
