<?php

declare(strict_types=1);

namespace App\Model;

use PDO;

class Comment
{
	public function __construct(private PDO $pdo)
	{
	}

	public function create(int $userId, int $imageId, string $content): int
	{
		$stmt = $this->pdo->prepare(
			"INSERT INTO comments (image_id, user_id, content)
			 VALUES (:image_id, :user_id, :content)"
		);
		$stmt->execute([
			":image_id" => $imageId,
			":user_id"  => $userId,
			":content"  => $content,
		]);

		return (int) $this->pdo->lastInsertId();
	}

	public function findByImageId(int $imageId): array
	{
		$stmt = $this->pdo->prepare(
			"SELECT comments.id, comments.image_id, comments.user_id,
			        comments.content, comments.created_at,
			        users.username
			 FROM comments
			 INNER JOIN users ON users.id = comments.user_id
			 WHERE comments.image_id = :image_id
			 ORDER BY comments.created_at ASC, comments.id ASC"
		);
		$stmt->execute([":image_id" => $imageId]);
		return $stmt->fetchAll();
	}

	public function countByImageId(int $imageId): int
	{
		$stmt = $this->pdo->prepare(
			"SELECT COUNT(*) FROM comments WHERE image_id = :image_id"
		);
		$stmt->execute([":image_id" => $imageId]);
		return (int) $stmt->fetchColumn();
	}
}
