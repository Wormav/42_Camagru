<?php

declare(strict_types=1);

namespace App\Core;

class SessionCore
{
	public static function start(): void
	{
		if (session_status() === PHP_SESSION_ACTIVE) {
			return;
		}

		session_set_cookie_params([
			"lifetime" => 0,
			"path"     => "/",
			"domain"   => "",
			"secure"   => !empty($_SERVER["HTTPS"]),
			"httponly" => true,
			"samesite" => "Lax",
		]);

		session_start();
	}

	public static function set(string $key, mixed $value): void
	{
		$_SESSION[$key] = $value;
	}

	public static function get(string $key, mixed $default = null): mixed
	{
		return $_SESSION[$key] ?? $default;
	}

	public static function has(string $key): bool
	{
		return isset($_SESSION[$key]);
	}

	public static function remove(string $key): void
	{
		unset($_SESSION[$key]);
	}

	public static function clear(): void
	{
		session_unset();
	}

	public static function regenerate(): void
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			return;
		}
		session_regenerate_id(true);
	}

	public static function destroy(): void
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			return;
		}

		$_SESSION = [];

		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				"",
				[
					"expires"  => time() - 42000,
					"path"     => $params["path"],
					"domain"   => $params["domain"],
					"secure"   => $params["secure"],
					"httponly" => $params["httponly"],
					"samesite" => $params["samesite"],
				],
			);
		}

		session_destroy();
	}
}
