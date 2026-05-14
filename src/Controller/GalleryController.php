<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Database;
use App\Model\Image;
use App\View\View;

class GalleryController
{
	private const PER_PAGE = 6;

	public function showGallery(): void
	{
		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new Database($dbConfig))->connection();
		$images   = new Image($pdo);

		$total      = $images->countAll();
		$totalPages = max(1, (int) ceil($total / self::PER_PAGE));

		$page = (int) ($_GET["page"] ?? 1);
		if ($page < 1) {
			$page = 1;
		}
		if ($page > $totalPages) {
			$page = $totalPages;
		}

		$offset        = ($page - 1) * self::PER_PAGE;
		$currentUserId = Auth::id();
		$items         = $images->findFeed(self::PER_PAGE, $offset, $currentUserId);

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("gallery/gallery", [
			"title"         => "Gallery",
			"items"         => $items,
			"total"         => $total,
			"page"          => $page,
			"totalPages"    => $totalPages,
			"isAuth"        => Auth::check(),
			"currentUserId" => $currentUserId,
		]);
	}

}
