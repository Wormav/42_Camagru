<?php

declare(strict_types=1);

namespace App\Model;

use PDO;

class Like
{
	public function __construct(private PDO $pdo)
	{
	}

	public function hasLiked(int $userId, int $imageId): bool
	{
		$stmt = $this->pdo->prepare(
			"SELECT 1 FROM likes
			 WHERE user_id = :user_id AND image_id = :image_id
			 LIMIT 1"
		);
		$stmt->execute([
		":user_id" => $userId,
		":image_id" => $imageId,
		]);

		return $stmt->fetchColumn() !== false;
	}

	public function toggle(int $userId, int $imageId): bool
	{
		if ($this->hasLiked($userId, $imageId)) {
			$stmt = $this->pdo->prepare(
				"DELETE FROM likes
				 WHERE user_id = :user_id AND image_id = :image_id"
			);
			$stmt->execute([
			":user_id" => $userId,
			":image_id" => $imageId,
			]);
			return false;
		}

		$stmt = $this->pdo->prepare(
			"INSERT INTO likes (user_id, image_id)
			 VALUES (:user_id, :image_id)"
		);
		$stmt->execute([
		":user_id" => $userId,
		":image_id" => $imageId,
		]);
		return true;
	}

	public function countByImageId(int $imageId): int
	{
		$stmt = $this->pdo->prepare(
			"SELECT COUNT(*) FROM likes WHERE image_id = :image_id"
		);
		$stmt->execute([":image_id" => $imageId]);
		return (int) $stmt->fetchColumn();
	}
}
