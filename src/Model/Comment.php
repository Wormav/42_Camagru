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
			        users.username, users.avatar_path
			 FROM comments
			 INNER JOIN users ON users.id = comments.user_id
			 WHERE comments.image_id = :image_id
			 ORDER BY comments.created_at ASC, comments.id ASC"
		);
		$stmt->execute([":image_id" => $imageId]);
		return $stmt->fetchAll();
	}

	public function findById(int $id): ?array
	{
		$stmt = $this->pdo->prepare(
			"SELECT id, image_id, user_id, content, created_at
			 FROM comments
			 WHERE id = :id LIMIT 1"
		);
		$stmt->execute([":id" => $id]);
		$row = $stmt->fetch();
		return $row !== false ? $row : null;
	}

	public function delete(int $id): bool
	{
		$stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = :id");
		$stmt->execute([":id" => $id]);
		return $stmt->rowCount() === 1;
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
