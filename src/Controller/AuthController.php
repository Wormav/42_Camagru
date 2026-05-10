<?php

declare(strict_types=1);

namespace App\Controller;

use App\View\View;

class AuthController
{
	public function showRegister(): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/register", [
			"title"  => "Sign up",
			"errors" => [],
			"old"    => [],
		]);
	}
}
