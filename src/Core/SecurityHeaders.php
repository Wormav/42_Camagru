<?php

declare(strict_types=1);

namespace App\Core;

class SecurityHeaders
{
	public static function send(): void
	{
		if (headers_sent()) {
			return;
		}

		header("X-Frame-Options: DENY");

		header("X-Content-Type-Options: nosniff");

		header("Referrer-Policy: strict-origin-when-cross-origin");

		header("Permissions-Policy: geolocation=(), payment=(), usb=()");

		$csp = "default-src 'self'; "
			. "script-src 'self'; "
			. "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
			. "img-src 'self' data: blob:; "
			. "media-src 'self' blob:; "
			. "font-src 'self' data: https://fonts.gstatic.com; "
			. "connect-src 'self'; "
			. "object-src 'none'; "
			. "base-uri 'self'; "
			. "form-action 'self'; "
			. "frame-ancestors 'none'";
		header("Content-Security-Policy: " . $csp);
	}
}
