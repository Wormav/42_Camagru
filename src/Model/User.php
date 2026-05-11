<?php

declare(strict_types=1);

namespace App\Model;

use PDO;

class User
{
	public function __construct(private PDO $pdo)
	{
	}

	public function findByEmail(string $email): ?array
	{
		$stmt = $this->pdo->prepare(
			"SELECT * FROM users WHERE email = :email LIMIT 1",
		);
		$stmt->execute([":email" => $email]);
		$row = $stmt->fetch();
		return $row !== false ? $row : null;
	}

	public function findByUsername(string $username): ?array
	{
		$stmt = $this->pdo->prepare(
			"SELECT * FROM users WHERE username = :username LIMIT 1",
		);
		$stmt->execute([":username" => $username]);
		$row = $stmt->fetch();
		return $row !== false ? $row : null;
	}

	public function findById(int $id): ?array
	{
		$stmt = $this->pdo->prepare(
			"SELECT * FROM users WHERE id = :id LIMIT 1",
		);
		$stmt->execute([":id" => $id]);
		$row = $stmt->fetch();
		return $row !== false ? $row : null;
	}

	public function create(
		string $email,
		string $username,
		string $passwordHash,
		string $verificationToken,
	): int {
		$stmt = $this->pdo->prepare(
			"INSERT INTO users (email, username, password, verification_token)
			 VALUES (:email, :username, :password, :token)",
		);
		$stmt->execute([
			":email"    => $email,
			":username" => $username,
			":password" => $passwordHash,
			":token"    => $verificationToken,
		]);
		return (int) $this->pdo->lastInsertId();
	}

	public function verifyByToken(string $token): bool
	{
		$stmt = $this->pdo->prepare(
			"UPDATE users
			 SET is_verified = TRUE, verification_token = NULL
			 WHERE verification_token = :token AND is_verified = FALSE",
		);
		$stmt->execute([":token" => $token]);
		return $stmt->rowCount() === 1;
	}

	public function setResetToken(int $userId, string $token, string $expiresAt): void
	{
		$stmt = $this->pdo->prepare(
			"UPDATE users
			 SET reset_token = :token, reset_token_expires = :expires
			 WHERE id = :id",
		);
		$stmt->execute([
			":token"   => $token,
			":expires" => $expiresAt,
			":id"      => $userId,
		]);
	}

	public function findByValidResetToken(string $token): ?array
	{
		$stmt = $this->pdo->prepare(
			"SELECT * FROM users
			 WHERE reset_token = :token
			   AND reset_token_expires IS NOT NULL
			   AND reset_token_expires > NOW()
			 LIMIT 1",
		);
		$stmt->execute([":token" => $token]);
		$row = $stmt->fetch();
		return $row !== false ? $row : null;
	}

	public function updatePasswordAndClearReset(int $userId, string $passwordHash): bool
	{
		$stmt = $this->pdo->prepare(
			"UPDATE users
			 SET password = :password,
			     reset_token = NULL,
			     reset_token_expires = NULL
			 WHERE id = :id",
		);
		$stmt->execute([
			":password" => $passwordHash,
			":id"       => $userId,
		]);
		return $stmt->rowCount() === 1;
	}

	public function updateUsername(int $userId, string $newUsername): bool
	{
		$stmt = $this->pdo->prepare(
			"UPDATE users SET username = :username WHERE id = :id",
		);
		$stmt->execute([
			":username" => $newUsername,
			":id"       => $userId,
		]);
		return $stmt->rowCount() === 1;
	}

	public function updateEmail(int $userId, string $newEmail): bool
	{
		$stmt = $this->pdo->prepare(
			"UPDATE users SET email = :email WHERE id = :id",
		);
		$stmt->execute([
			":email" => $newEmail,
			":id"    => $userId,
		]);
		return $stmt->rowCount() === 1;
	}

	public function updatePassword(int $userId, string $passwordHash): bool
	{
		$stmt = $this->pdo->prepare(
			"UPDATE users SET password = :password WHERE id = :id",
		);
		$stmt->execute([
			":password" => $passwordHash,
			":id"       => $userId,
		]);
		return $stmt->rowCount() === 1;
	}

	public function updateNotifyComments(int $userId, bool $notify): bool
	{
		$stmt = $this->pdo->prepare(
			"UPDATE users SET notify_comments = :notify WHERE id = :id",
		);
		$stmt->execute([
			":notify" => $notify ? 1 : 0,
			":id"     => $userId,
		]);
		return $stmt->rowCount() <= 1;
	}

	public function updateAvatar(int $userId, ?string $avatarPath): bool
	{
		$stmt = $this->pdo->prepare(
			"UPDATE users SET avatar_path = :path WHERE id = :id",
		);
		$stmt->execute([
			":path" => $avatarPath,
			":id"   => $userId,
		]);
		return $stmt->rowCount() === 1;
	}
}
