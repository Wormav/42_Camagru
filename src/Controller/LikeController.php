<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Model\Image;
use App\Model\Like;

class LikeController
{
	public function toggle(): void
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
		if ($imageId <= 0) {
			$this->jsonError(400, "Missing image id.");
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo = (new Database($dbConfig))->connection();

		if ((new Image($pdo))->findById($imageId) === null) {
			$this->jsonError(404, "Image not found.");
		}

		$userId = (int) Auth::id();
		$like = new Like($pdo);

		$liked = $like->toggle($userId, $imageId);
		$likeCount = $like->countByImageId($imageId);

		$this->jsonSuccess([
			"image_id" => $imageId,
			"liked"  => $liked,
			"like_count" => $likeCount,
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
