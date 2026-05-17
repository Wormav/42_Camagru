<?php

declare(strict_types=1);

use App\Core\EnvCore;

return [
	"host"     => EnvCore::get("MAIL_HOST", "localhost"),
	"port"     => (int) EnvCore::get("MAIL_PORT", "587"),
	"username" => EnvCore::get("MAIL_USERNAME", ""),
	"password" => EnvCore::get("MAIL_PASSWORD", ""),
	"from"     => EnvCore::get("MAIL_FROM", "no-reply@camagru.local"),
	"app_url"  => EnvCore::get("APP_URL", "http://localhost:8080"),
];
