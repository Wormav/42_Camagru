<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AuthCore;
use App\Core\DatabaseCore;
use App\Core\JsonCore;
use App\Model\CommentModel;
use App\Model\ImageModel;
use App\View\View;

class GalleryController
{
	private const PER_PAGE = 6;

	public function showGallery(): void
	{
		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new DatabaseCore($dbConfig))->connection();
		$images   = new ImageModel($pdo);

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
		$currentUserId = AuthCore::id();
		$items         = $images->findFeed(self::PER_PAGE, $offset, $currentUserId);

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("gallery/galleryTemplate", [
			"title"         => "Gallery",
			"scripts"       => ["/dist/gallery.bundle.js"],
			"items"         => $items,
			"total"         => $total,
			"page"          => $page,
			"totalPages"    => $totalPages,
			"isAuth"        => AuthCore::check(),
			"currentUserId" => $currentUserId,
		]);
	}

	public function feedJson(): void
	{
		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new DatabaseCore($dbConfig))->connection();
		$images   = new ImageModel($pdo);

		$total      = $images->countAll();
		$totalPages = max(1, (int) ceil($total / self::PER_PAGE));

		$page = (int) ($_GET["page"] ?? 1);
		if ($page < 1) {
			$page = 1;
		}

		if ($page > $totalPages) {
			JsonCore::success([
				"html"    => "",
				"page"    => $page,
				"hasMore" => false,
			]);
		}

		$offset        = ($page - 1) * self::PER_PAGE;
		$currentUserId = AuthCore::id();
		$items         = $images->findFeed(self::PER_PAGE, $offset, $currentUserId);
		$isAuth        = AuthCore::check();

		$e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, "UTF-8");

		ob_start();
		foreach ($items as $item) {
			include __DIR__ . "/../View/templates/gallery/_cardTemplate.php";
		}
		$html = (string) ob_get_clean();

		JsonCore::success([
			"html"    => $html,
			"page"    => $page,
			"hasMore" => $page < $totalPages,
		]);
	}

	public function showImage(): void
	{
		$imageId = (int) ($_GET["id"] ?? 0);
		if ($imageId <= 0) {
			header("Location: /gallery");
			exit;
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new DatabaseCore($dbConfig))->connection();

		$currentUserId = AuthCore::id();
		$images        = new ImageModel($pdo);

		$item = $images->findOneEnriched($imageId, $currentUserId);
		if ($item === null) {
			header("Location: /gallery");
			exit;
		}

		$comments = (new CommentModel($pdo))->findByImageId($imageId);

		$scheme = (
			(!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off")
			|| (($_SERVER["HTTP_X_FORWARDED_PROTO"] ?? "") === "https")
		) ? "https" : "http";
		$host    = (string) ($_SERVER["HTTP_HOST"] ?? "localhost");
		$baseUrl = $scheme . "://" . $host;

		$pageUrl  = $baseUrl . "/image?id=" . $imageId;
		$imageUrl = $baseUrl . (string) $item["image_path"];

		$shareText = "Check out this snap by @" . $item["username"] . " on Camagru";

		$share = [
			"twitter" => "https://twitter.com/intent/tweet?url=" . rawurlencode($pageUrl) . "&text=" . rawurlencode($shareText),
			"link"    => $pageUrl,
		];

		$og = [
			"title"       => "Snap by @" . $item["username"] . " — Camagru",
			"description" => "A photobooth snap shared on Camagru.",
			"image"       => $imageUrl,
			"url"         => $pageUrl,
		];

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("gallery/detailTemplate", [
			"title"         => "Snap by @" . $item["username"],
			"scripts"       => ["/dist/gallery.bundle.js"],
			"item"          => $item,
			"comments"      => $comments,
			"isAuth"        => AuthCore::check(),
			"currentUserId" => $currentUserId,
			"og"            => $og,
			"share"         => $share,
		]);
	}
}
