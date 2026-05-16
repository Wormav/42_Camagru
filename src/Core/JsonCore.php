<?php

declare(strict_types=1);

namespace App\Core;

class JsonCore
{
	public static function success(array $payload = [], int $status = 200): never
	{
		self::send($status, ["ok" => true] + $payload);
	}

	public static function error(int $status, string $message): never
	{
		self::send($status, ["ok" => false, "error" => $message]);
	}

	private static function send(int $status, array $body): never
	{
		http_response_code($status);
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($body, JSON_THROW_ON_ERROR);
		exit;
	}
}
