<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Database;
use App\Model\Image;
use App\View\View;

class HomeController
{
	public function index(): void
	{
		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new Database($dbConfig))->connection();

		$totalSnaps = (new Image($pdo))->countAll();

		$creatorsStmt  = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM images");
		$totalCreators = (int) $creatorsStmt->fetchColumn();

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("home", [
			"title"         => "Camagru — photo booth",
			"totalSnaps"    => $totalSnaps,
			"totalCreators" => $totalCreators,
			"isAuth"        => Auth::check(),
		]);
	}
}
