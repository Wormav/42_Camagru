<?php

declare(strict_types=1);

use App\Core\Env;

return [
	"host"     => Env::get("MAIL_HOST", "localhost"),
	"port"     => (int) Env::get("MAIL_PORT", "587"),
	"username" => Env::get("MAIL_USERNAME", ""),
	"password" => Env::get("MAIL_PASSWORD", ""),
	"from"     => Env::get("MAIL_FROM", "no-reply@camagru.local"),
	"app_url"  => Env::get("APP_URL", "http://localhost:8000"),
];
