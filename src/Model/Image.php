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

	public function findByUserId(int $userId): array
	{
		$stmt = $this->pdo->prepare(
			"SELECT id, user_id, image_path, overlay_used, created_at
			FROM images
			WHERE user_id = :user_id
			ORDER BY created_at DESC, id DESC"
		);
		$stmt->execute([":user_id" => $userId]);
		return $stmt->fetchAll();
	}

	public function findById(int $id): ?array
	{
		$stmt = $this->pdo->prepare(
			"SELECT id, user_id, image_path, overlay_used, created_at
			FROM images
			WHERE id = :id LIMIT 1",
		);
		$stmt->execute([":id" => $id]);
		$row = $stmt->fetch();
		return $row !== false ? $row : null;
	}

	public function delete(int $id): bool
	{
		$stmt = $this->pdo->prepare(
			"DELETE FROM images WHERE id = :id",
		);
		$stmt->execute([":id" => $id]);
		return $stmt->rowCount() === 1;
	}

	public function findFeed(int $limit, int $offset): array
	{
		$stmt = $this->pdo->prepare(
			"SELECT images.id, images.user_id, images.image_path, images.overlay_used, images.created_at, users.username,
			COUNT(DISTINCT likes.id) AS like_count,
			COUNT(DISTINCT comments.id) AS comment_count
			FROM images
			JOIN users ON images.user_id = users.id
			LEFT JOIN likes ON images.id = likes.image_id
			LEFT JOIN comments ON images.id = comments.image_id
			GROUP BY images.id
			ORDER BY images.created_at DESC, images.id DESC
			LIMIT :limit OFFSET :offset"
		);
		$stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
		$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function countAll(): int
	{
		$stmt = $this->pdo->query("SELECT COUNT(*) FROM images");
		return (int) $stmt->fetchColumn();
	}
}
