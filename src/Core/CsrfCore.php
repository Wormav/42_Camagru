<?php

declare(strict_types=1);

namespace App\Core;

class CsrfCore
{
	private const SESSION_KEY = "csrf_token";
	private const FIELD_NAME  = "csrf_token";

	public static function token(): string
	{
		SessionCore::start();

		if (!SessionCore::has(self::SESSION_KEY)) {
			SessionCore::set(self::SESSION_KEY, bin2hex(random_bytes(32)));
		}

		return SessionCore::get(self::SESSION_KEY);
	}

	public static function validate(string $submitted): bool
	{
		$expected = SessionCore::get(self::SESSION_KEY, "");
		return hash_equals($expected, $submitted);
	}

	public static function field(): string
	{
		$token = htmlspecialchars(self::token(), ENT_QUOTES, "UTF-8");
		return sprintf(
			'<input type="hidden" name="%s" value="%s">',
			self::FIELD_NAME,
			$token,
		);
	}

	public static function fieldName(): string
	{
		return self::FIELD_NAME;
	}
}
