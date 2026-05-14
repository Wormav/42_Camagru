<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Model\Comment;
use App\Model\Image;
use App\Model\User;
use App\Service\Mailer;
use Throwable;

class CommentController
{
	private const CONTENT_MAX_LENGTH = 500;

	public function create(): void
	{
		if (!Auth::check()) {
			$this->jsonError(401, "Sign in required.");
		}

		$submittedToken = is_string($_POST[Csrf::fieldName()] ?? null)
			? $_POST[Csrf::fieldName()]
			: "";
		if (!Csrf::validate($submittedToken)) {
			$this->jsonError(403, "Invalid CSRF token.");
		}

		$imageId = (int) ($_POST["image_id"] ?? 0);
		$content = trim((string) ($_POST["content"] ?? ""));

		if ($imageId <= 0) {
			$this->jsonError(400, "Missing image.");
		}
		if ($content === "") {
			$this->jsonError(400, "Comment cannot be empty.");
		}
		if (mb_strlen($content) > self::CONTENT_MAX_LENGTH) {
			$this->jsonError(400, "Comment is too long (max " . self::CONTENT_MAX_LENGTH . " characters).");
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new Database($dbConfig))->connection();

		$images = new Image($pdo);
		$image  = $images->findById($imageId);
		if ($image === null) {
			$this->jsonError(404, "Image not found.");
		}

		$commenterId = (int) Auth::id();
		$comments    = new Comment($pdo);
		$commentId   = $comments->create($commenterId, $imageId, $content);

		$row = $comments->findByIdEnriched($commentId);
		if ($row === null) {
			$this->jsonError(500, "Could not load created comment.");
		}

		$this->maybeSendNotification($pdo, (int) $image["user_id"], $commenterId, $imageId, $content);

		$createdTs    = strtotime((string) $row["created_at"]);
		$createdIso   = $createdTs !== false ? date("c", $createdTs) : "";
		$createdHuman = $createdTs !== false ? date("M j, H:i", $createdTs) : "";

		$this->jsonSuccess([
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

		$users  = new User($pdo);
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
			(new Mailer($mailConfig))->sendCommentNotification(
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
		if (!Auth::check()) {
			$this->jsonError(401, "Sign in required.");
		}

		$submittedToken = is_string($_POST[Csrf::fieldName()] ?? null)
			? $_POST[Csrf::fieldName()]
			: "";
		if (!Csrf::validate($submittedToken)) {
			$this->jsonError(403, "Invalid CSRF token.");
		}

		$commentId = (int) ($_POST["comment_id"] ?? 0);
		if ($commentId <= 0) {
			$this->jsonError(400, "Missing comment.");
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new Database($dbConfig))->connection();

		$comments = new Comment($pdo);
		$comment  = $comments->findById($commentId);
		if ($comment === null) {
			$this->jsonError(404, "Comment not found.");
		}

		if ((int) $comment["user_id"] !== (int) Auth::id()) {
			$this->jsonError(403, "You can only delete your own comments.");
		}

		if (!$comments->delete($commentId)) {
			$this->jsonError(500, "Could not delete comment.");
		}

		$imageId = (int) $comment["image_id"];

		$this->jsonSuccess([
			"comment_id"    => $commentId,
			"image_id"      => $imageId,
			"comment_count" => $comments->countByImageId($imageId),
		]);
	}

	private function jsonError(int $status, string $message): void
	{
		http_response_code($status);
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode(["ok" => false, "error" => $message], JSON_THROW_ON_ERROR);
		exit;
	}

	private function jsonSuccess(array $payload = []): void
	{
		http_response_code(200);
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode(["ok" => true] + $payload, JSON_THROW_ON_ERROR);
		exit;
	}
}
