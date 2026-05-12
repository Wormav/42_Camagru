<?php

declare(strict_types=1);

namespace App\Model;

use PDO;

class Image
{
	public function __construct(private PDO $pdo)
	{
	}

	public function create(int $userId, string $imagePath, string $overlayUsed): int
	{
		$stmt = $this->pdo->prepare(
			"INSERT INTO images (user_id, image_path, overlay_used)
		VALUES (:user_id, :image_path, :overlay_used)"
		);

		$stmt->execute([
		":user_id" => $userId,
		":image_path"   => $imagePath,
		":overlay_used"   => $overlayUsed,
		]);

		return (int) $this->pdo->lastInsertId();
	}
}
