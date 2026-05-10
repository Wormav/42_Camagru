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
}
