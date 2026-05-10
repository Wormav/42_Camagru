<?php

declare(strict_types=1);

namespace App\Model;

use PDO;

class User
{
	public function __construct(private PDO $pdo)
	{
	}

	/**
	 * @return array<string, mixed>|null
	 */
	public function findByEmail(string $email): ?array
	{
		$stmt = $this->pdo->prepare(
			"SELECT * FROM users WHERE email = :email LIMIT 1",
		);
		$stmt->execute([":email" => $email]);
		$row = $stmt->fetch();
		return $row !== false ? $row : null;
	}

	/**
	 * @return array<string, mixed>|null
	 */
	public function findByUsername(string $username): ?array
	{
		$stmt = $this->pdo->prepare(
			"SELECT * FROM users WHERE username = :username LIMIT 1",
		);
		$stmt->execute([":username" => $username]);
		$row = $stmt->fetch();
		return $row !== false ? $row : null;
	}

	/**
	 * Insert a new user. Returns the generated id.
	 *
	 * The caller MUST hash the password (bcrypt) and provide a one-time
	 * verification token; this method does no security work of its own.
	 */
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

	/**
	 * Activate a user account using its verification token. Returns true
	 * if a row was flipped, false otherwise (token unknown or already used).
	 */
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
}
