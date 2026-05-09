<?php

declare(strict_types=1);

namespace App\Core;

class Env
{
	private static array $values = [];
	private static bool $loaded = false;

	public static function load(string $path): void
	{
		if (self::$loaded) {
			return;
		}

		if (!is_file($path)) {
			throw new \RuntimeException("Env file not found: {$path}");
		}

		$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if ($lines === false) {
			throw new \RuntimeException("Cannot read env file: {$path}");
		}

		foreach ($lines as $line) {
			$trimmed = ltrim($line);

			// Skip comments and lines without an assignment.
			if ($trimmed === "" || str_starts_with($trimmed, "#")) {
				continue;
			}
			if (!str_contains($line, "=")) {
				continue;
			}

			[$key, $value] = explode("=", $line, 2);
			$key = trim($key);
			$value = trim($value);

			// Strip a single pair of surrounding quotes if present.
			if (strlen($value) >= 2) {
				$first = $value[0];
				$last = $value[strlen($value) - 1];
				if (($first === "\"" && $last === "\"") || ($first === "'" && $last === "'")) {
					$value = substr($value, 1, -1);
				}
			}

			if ($key !== "") {
				self::$values[$key] = $value;
			}
		}

		self::$loaded = true;
	}

	public static function get(string $key, ?string $default = null): ?string
	{
		return self::$values[$key] ?? $default;
	}
}
