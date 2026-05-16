<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AuthCore;
use App\Core\CsrfCore;
use App\Core\DatabaseCore;
use App\Core\JsonCore;
use App\Model\ImageModel;
use App\Model\LikeModel;

class LikeController
{
	public function toggle(): void
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
		if ($imageId <= 0) {
			JsonCore::error(400, "Missing image id.");
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo = (new DatabaseCore($dbConfig))->connection();

		if ((new ImageModel($pdo))->findById($imageId) === null) {
			JsonCore::error(404, "Image not found.");
		}

		$userId = (int) AuthCore::id();
		$like = new LikeModel($pdo);

		$liked = $like->toggle($userId, $imageId);
		$likeCount = $like->countByImageId($imageId);

		JsonCore::success([
			"image_id" => $imageId,
			"liked"  => $liked,
			"like_count" => $likeCount,
		]);
	}

}
