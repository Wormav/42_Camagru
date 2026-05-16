<?php

declare(strict_types=1);

use App\Core\EnvCore;

return [
	"driver" => "mysql",
	"host" => EnvCore::get("DB_HOST", "127.0.0.1"),
	"port" => (int) EnvCore::get("DB_PORT", "3306"),
	"name" => EnvCore::get("DB_NAME", ""),
	"user" => EnvCore::get("DB_USER", ""),
	"pass" => EnvCore::get("DB_PASS", ""),
	"charset" => "utf8mb4",
];
