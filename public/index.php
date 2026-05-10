<?php

declare(strict_types=1);

// Let PHP's built-in dev server serve static files directly (CSS, images, JS).
// In production (Nginx), this is handled by `try_files` and never reached.
if (PHP_SAPI === "cli-server") {
	$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
	if ($path !== "/" && $path !== false && is_file(__DIR__ . $path)) {
		return false;
	}
}

require __DIR__ . "/../vendor/autoload.php";

use App\Controller\AuthController;
use App\Controller\HomeController;
use App\Core\Env;
use App\Core\Router;
use App\Core\Session;

Env::load(__DIR__ . "/../.env");
Session::start();

$router = new Router();

$router->get("/", [HomeController::class, "index"]);
$router->get("/register", [AuthController::class, "showRegister"]);

$router->dispatch($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);
