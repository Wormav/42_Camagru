<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Flash;
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
		Auth::requireAuth();

		$submittedToken = is_string($_POST[Csrf::fieldName()] ?? null)
			? $_POST[Csrf::fieldName()]
			: "";
		if (!Csrf::validate($submittedToken)) {
			Flash::set("error", "Invalid CSRF token.");
			$this->redirectBack();
		}

		$imageId = (int) ($_POST["image_id"] ?? 0);
		$content = trim((string) ($_POST["content"] ?? ""));

		if ($imageId <= 0) {
			Flash::set("error", "Missing image.");
			$this->redirectBack();
		}
		if ($content === "") {
			Flash::set("error", "Comment cannot be empty.");
			$this->redirectBack($imageId);
		}
		if (mb_strlen($content) > self::CONTENT_MAX_LENGTH) {
			Flash::set("error", "Comment is too long (max " . self::CONTENT_MAX_LENGTH . " characters).");
			$this->redirectBack($imageId);
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new Database($dbConfig))->connection();

		$images = new Image($pdo);
		$image  = $images->findById($imageId);
		if ($image === null) {
			Flash::set("error", "Image not found.");
			$this->redirectBack();
		}

		$commenterId = (int) Auth::id();
		(new Comment($pdo))->create($commenterId, $imageId, $content);

		Flash::set("success", "Comment posted.");

		$this->maybeSendNotification($pdo, (int) $image["user_id"], $commenterId, $imageId, $content);

		$this->redirectBack($imageId);
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

		$commenter = $users->findById($commenterId);
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
		Auth::requireAuth();

		$submittedToken = is_string($_POST[Csrf::fieldName()] ?? null)
			? $_POST[Csrf::fieldName()]
			: "";
		if (!Csrf::validate($submittedToken)) {
			Flash::set("error", "Invalid CSRF token.");
			$this->redirectBack();
		}

		$commentId = (int) ($_POST["comment_id"] ?? 0);
		if ($commentId <= 0) {
			Flash::set("error", "Missing comment.");
			$this->redirectBack();
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new Database($dbConfig))->connection();

		$comments = new Comment($pdo);
		$comment  = $comments->findById($commentId);
		if ($comment === null) {
			Flash::set("error", "Comment not found.");
			$this->redirectBack();
		}

		if ((int) $comment["user_id"] !== (int) Auth::id()) {
			Flash::set("error", "You can only delete your own comments.");
			$this->redirectBack((int) $comment["image_id"]);
		}

		$comments->delete($commentId);
		Flash::set("success", "Comment deleted.");
		$this->redirectBack((int) $comment["image_id"]);
	}

	private function redirectBack(?int $imageId = null): void
	{
		$location = $imageId !== null ? "/image?id=" . $imageId : "/gallery";
		header("Location: {$location}");
		exit;
	}
}
