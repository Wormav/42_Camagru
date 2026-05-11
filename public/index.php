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
use App\Controller\PostController;
use App\Controller\ProfileController;
use App\Core\Env;
use App\Core\Router;
use App\Core\Session;

Env::load(__DIR__ . "/../.env");
Session::start();

$router = new Router();

$router->get("/", [HomeController::class, "index"]);
$router->get("/register", [AuthController::class, "showRegister"]);
$router->post("/register", [AuthController::class, "register"]);
$router->get("/register/check-email", [AuthController::class, "showCheckEmail"]);
$router->get("/verify", [AuthController::class, "verify"]);
$router->get("/login", [AuthController::class, "showLogin"]);
$router->post("/login", [AuthController::class, "login"]);
$router->post("/logout", [AuthController::class, "logout"]);
$router->get("/reset", [AuthController::class, "showForgotPassword"]);
$router->post("/reset", [AuthController::class, "forgotPassword"]);
$router->get("/reset/sent", [AuthController::class, "showForgotPasswordSent"]);
$router->get("/reset/confirm", [AuthController::class, "showResetPassword"]);
$router->post("/reset/confirm", [AuthController::class, "resetPassword"]);
$router->get("/profile", [ProfileController::class, "showProfile"]);
$router->post("/profile/username", [ProfileController::class, "updateUsername"]);
$router->post("/profile/email", [ProfileController::class, "updateEmail"]);
$router->post("/profile/password", [ProfileController::class, "updatePassword"]);
$router->post("/profile/notifications", [ProfileController::class, "updateNotifications"]);
$router->post("/profile/avatar", [ProfileController::class, "updateAvatar"]);
$router->get("/post", [PostController::class, "showPost"]);
$router->post("/post/capture", [PostController::class, "capture"]);

$router->dispatch($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);
