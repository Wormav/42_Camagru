<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AuthCore;
use App\Core\CsrfCore;
use App\Core\DatabaseCore;
use App\Model\ImageModel;
use App\Model\LikeModel;

class LikeController
{
	public function toggle(): void
	{
		if (!AuthCore::check()) {
			$this->jsonError(401, "Sign in required.");
		}

		$submittedToken = is_string($_POST[CsrfCore::fieldName()] ?? null)
			? $_POST[CsrfCore::fieldName()]
			: "";
		if (!CsrfCore::validate($submittedToken)) {
			$this->jsonError(403, "Invalid CSRF token.");
		}

		$imageId = (int) ($_POST["image_id"] ?? 0);
		if ($imageId <= 0) {
			$this->jsonError(400, "Missing image id.");
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo = (new DatabaseCore($dbConfig))->connection();

		if ((new ImageModel($pdo))->findById($imageId) === null) {
			$this->jsonError(404, "ImageModel not found.");
		}

		$userId = (int) AuthCore::id();
		$like = new LikeModel($pdo);

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
