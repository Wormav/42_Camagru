<?php

declare(strict_types=1);

namespace App\Core;

class FilenameCore
{
	public static function randomized(string $extension, string $prefix = ""): string
	{
		$timestamp = date("YmdHis");
		$random = bin2hex(random_bytes(8));
		return $prefix . $timestamp . "_" .$random . "." . $extension;
	}
}
