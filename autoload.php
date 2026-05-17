<?php

declare(strict_types=1);

//maps "App\\..." class names to files under src/.
spl_autoload_register(function (string $class): void {
	$prefix = "App\\";
	if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
		return;
	}
	$file = __DIR__ . "/src/" . str_replace("\\", "/", substr($class, strlen($prefix))) . ".php";
	if (is_file($file)) {
		require $file;
	}
});
