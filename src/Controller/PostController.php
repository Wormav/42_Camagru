<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AuthCore;
use App\Core\CsrfCore;
use App\Core\DatabaseCore;
use App\Core\JsonCore;
use App\Core\ValidatorCore;
use App\Model\ImageModel;
use App\Service\ImageComposerService;
use App\View\View;

class PostController
{
	private const SNAPS_DIR           = __DIR__ . "/../../public/uploads/snaps";
	private const SNAPS_PUBLIC_PREFIX = "/uploads/snaps/";

	public function showPost(): void
	{
		AuthCore::requireAuth();

		$overlays = require __DIR__ . "/../../config/overlays.php";
		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo = (new DatabaseCore($dbConfig))->connection();
		$userImages = (new ImageModel($pdo))->findByUserId((int) AuthCore::id());

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("post/postTemplate", [
			"title"    => "Post",
			"scripts"  => ["/dist/post.bundle.js"],
			"overlays" => $overlays,
			"userImages" => $userImages,
		]);
	}

	public function capture(): void
	{
		AuthCore::requireAuth();

		$submittedToken = is_string($_POST[CsrfCore::fieldName()] ?? null)
			? $_POST[CsrfCore::fieldName()]
			: "";
		if (!CsrfCore::validate($submittedToken)) {
			JsonCore::error(403, "Invalid CSRF token.");
		}

		$submittedId = is_string($_POST["overlay_id"] ?? null) ? $_POST["overlay_id"] : "";
		$overlays    = require __DIR__ . "/../../config/overlays.php";
		if (!isset($overlays[$submittedId])) {
			JsonCore::error(400, "Unknown overlay.");
		}
		$overlay = $overlays[$submittedId];

		$file = $_FILES["snap"] ?? null;
		if (!is_array($file)) {
			JsonCore::error(400, "No snap submitted.");
		}

		$validationError = ValidatorCore::validateSnapUpload($file);
		if ($validationError !== null) {
			JsonCore::error(400, $validationError);
		}

		$overlayAbsPath = __DIR__ . "/../../public" . $overlay["path"];

		$composer = new ImageComposerService();
		$fileName = $composer->merge($file["tmp_name"], $overlayAbsPath, self::SNAPS_DIR);
		if ($fileName === null) {
			JsonCore::error(500, "Failed to compose snap.");
		}

		$publicPath = self::SNAPS_PUBLIC_PREFIX . $fileName;
		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo = (new DatabaseCore($dbConfig))->connection();
		$imageId = (new ImageModel($pdo))->create((int) AuthCore::id(), $publicPath, $overlay["id"]);

		JsonCore::success([
			"image_id"   => $imageId,
			"image_path" => $publicPath,
			"overlay_id" => $overlay["id"],
		]);
	}

	public function delete(): void
	{
		AuthCore::requireAuth();

		$submittedToken = is_string($_POST[CsrfCore::fieldName()] ?? null)
			? $_POST[CsrfCore::fieldName()]
			: "";
		if (!CsrfCore::validate($submittedToken)) {
			JsonCore::error(403, "Invalid CSRF token.");
		}

		$imageId = (int) ($_POST["image_id"] ?? 0);
		if ($imageId <= 0) {
			JsonCore::error(400, "Missing image id.");
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo = (new DatabaseCore($dbConfig))->connection();
		$images = new ImageModel($pdo);

		$image = $images->findById($imageId);
		if ($image === null) {
			JsonCore::error(404, "Image not found.");
		}
		if ((int) $image["user_id"] !== (int) AuthCore::id()) {
			JsonCore::error(403, "Forbidden.");
		}

		$absPath = __DIR__ . "/../../public" . $image["image_path"];
		if (is_file($absPath) && !@unlink($absPath)) {
			JsonCore::error(500, "Could not delete file.");
		}

		if (!$images->delete($imageId)) {
			JsonCore::error(500, "Could not delete record.");
		}

		JsonCore::success(["image_id" => $imageId]);
	}

}
