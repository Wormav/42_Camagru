<?php

declare(strict_types=1);

namespace App\Core;

class FlashCore
{
	private const ALLOWED_TYPES = ["success", "error"];
	private const KEY_PREFIX = "flash_";

	// green toast
	public static function success(string $message): void
	{
		self::set("success", $message);
	}

	// red toast
	public static function error(string $message): void
	{
		self::set("error", $message);
	}

	// type can be "success" or "error"
	public static function set(string $type, string $message): void
	{
		self::assertValidType($type);
		SessionCore::set(self::KEY_PREFIX . $type, $message);
	}

	// Read message and short display
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

	// Check if there's a message of the given type
	private static function assertValidType(string $type): void
	{
		if (!in_array($type, self::ALLOWED_TYPES, true)) {
			throw new \InvalidArgumentException("Flash type must be one of: " . implode(", ", self::ALLOWED_TYPES));
		}
	}
}
