<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;
use App\Model\User;
use App\View\View;

class ProfileController
{
	public function showProfile(): void
	{
		Auth::requireAuth();

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new Database($dbConfig))->connection();
		$users    = new User($pdo);

		$currentUser = $users->findById((int) Auth::id());

		if ($currentUser === null) {
			Session::destroy();
			header("location: /login");
			exit;
		}

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("profile/profile", [
			"title"       => "Profile",
			"currentUser" => $currentUser,
		]);
	}
}
