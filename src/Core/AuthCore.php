<?php

declare(strict_types=1);

namespace App\Core;

class AuthCore
{
	private const KEY_USER_ID     = "user_id";
	private const KEY_USERNAME    = "username";
	private const KEY_AVATAR_PATH = "avatar_path";

	public static function check(): bool
	{
		SessionCore::start();
		return SessionCore::has(self::KEY_USER_ID);
	}

	public static function id(): ?int
	{
		SessionCore::start();
		$id = SessionCore::get(self::KEY_USER_ID);
		return is_int($id) ? $id : null;
	}

	public static function username(): ?string
	{
		SessionCore::start();
		$username = SessionCore::get(self::KEY_USERNAME);
		return is_string($username) ? $username : null;
	}

	public static function avatarPath(): ?string
	{
		SessionCore::start();
		$path = SessionCore::get(self::KEY_AVATAR_PATH);
		return is_string($path) && $path !== "" ? $path : null;
	}

	public static function requireGuest(string $redirect = "/"): void
	{
		if (self::check()) {
			header("Location: {$redirect}");
			exit;
		}
	}

	public static function requireAuth(string $redirect = "/login"): void
	{
		if (!self::check()) {
			header("Location: {$redirect}");
			exit;
		}
	}
}
