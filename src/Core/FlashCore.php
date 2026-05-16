<?php

declare(strict_types=1);

namespace App\Core;

class FlashCore
{
	private const ALLOWED_TYPES = ["success", "error"];
	private const KEY_PREFIX    = "flash_";

	public static function success(string $message): void
	{
		self::set("success", $message);
	}

	public static function error(string $message): void
	{
		self::set("error", $message);
	}

	public static function set(string $type, string $message): void
	{
		self::assertValidType($type);
		SessionCore::set(self::KEY_PREFIX . $type, $message);
	}

	public static function get(string $type = "success"): ?string
	{
		self::assertValidType($type);

		$key = self::KEY_PREFIX . $type;
		if (!SessionCore::has($key)) {
			return null;
		}

		$value = SessionCore::get($key);
		SessionCore::remove($key);
		return is_string($value) ? $value : null;
	}

	private static function assertValidType(string $type): void
	{
		if (!in_array($type, self::ALLOWED_TYPES, true)) {
			throw new \InvalidArgumentException(
				"FlashCore type must be one of: "
				. implode(", ", self::ALLOWED_TYPES),
			);
		}
	}
}
