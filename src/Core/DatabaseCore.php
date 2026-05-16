<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class DatabaseCore
{
	private array $config;
	private ?PDO $pdo = null;

	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function connection(): PDO
	{
		if ($this->pdo !== null) {
			return $this->pdo;
		}

		$dsn = sprintf(
			"%s:host=%s;port=%d;dbname=%s;charset=%s",
			$this->config["driver"],
			$this->config["host"],
			$this->config["port"],
			$this->config["name"],
			$this->config["charset"],
		);

		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		];

		try {
			$this->pdo = new PDO(
				$dsn,
				$this->config["user"],
				$this->config["pass"],
				$options,
			);
		} catch (PDOException $e) {
			throw new RuntimeException(
				"Database connection failed: " . $e->getMessage(),
				(int) $e->getCode(),
				$e,
			);
		}

		return $this->pdo;
	}
}
