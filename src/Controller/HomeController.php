<?php

declare(strict_types=1);

namespace App\Controller;

use App\View\View;

class HomeController
{
	public function index(): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("home", [
			"title" => "Bienvenue sur Camagru",
		]);
	}
}
