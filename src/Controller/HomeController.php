<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AuthCore;
use App\Core\DatabaseCore;
use App\Model\ImageModel;
use App\View\View;

class HomeController
{
	public function index(): void
	{
		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new DatabaseCore($dbConfig))->connection();

		$totalSnaps = (new ImageModel($pdo))->countAll();

		$creatorsStmt  = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM images");
		$totalCreators = (int) $creatorsStmt->fetchColumn();

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("homeTemplate", [
			"title"         => "Camagru — photo booth",
			"totalSnaps"    => $totalSnaps,
			"totalCreators" => $totalCreators,
			"isAuth"        => AuthCore::check(),
		]);
	}
}
