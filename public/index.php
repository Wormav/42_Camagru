<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

use App\Controller\HomeController;
use App\Core\Router;

$router = new Router();

$router->get("/", [HomeController::class, "index"]);

$router->dispatch($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);
