<?php

declare(strict_types=1);

namespace App\Core;

class Auth
{
	private const KEY_USER_ID  = "user_id";
	private const KEY_USERNAME = "username";

	public static function check(): bool
	{
		Session::start();
		return Session::has(self::KEY_USER_ID);
	}

	public static function id(): ?int
	{
		Session::start();
		$id = Session::get(self::KEY_USER_ID);
		return is_int($id) ? $id : null;
	}

	public static function username(): ?string
	{
		Session::start();
		$username = Session::get(self::KEY_USERNAME);
		return is_string($username) ? $username : null;
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
