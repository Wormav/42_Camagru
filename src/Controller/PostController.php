<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Validator;
use App\Model\Image;
use App\Service\ImageComposer;
use App\View\View;

class PostController
{
	private const SNAPS_DIR           = __DIR__ . "/../../public/uploads/snaps";
	private const SNAPS_PUBLIC_PREFIX = "/uploads/snaps/";

	public function showPost(): void
	{
		Auth::requireAuth();

		$overlays = require __DIR__ . "/../../config/overlays.php";
		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo = (new Database($dbConfig))->connection();
		$userImages = (new Image($pdo))->findByUserId((int) Auth::id());

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("post/post", [
			"title"    => "Post",
			"scripts"  => ["/dist/post.bundle.js"],
			"overlays" => $overlays,
			"userImages" => $userImages,
		]);
	}

	public function capture(): void
	{
		Auth::requireAuth();

		$submittedToken = is_string($_POST[Csrf::fieldName()] ?? null)
			? $_POST[Csrf::fieldName()]
			: "";
		if (!Csrf::validate($submittedToken)) {
			$this->jsonError(403, "Invalid CSRF token.");
		}

		$submittedId = is_string($_POST["overlay_id"] ?? null) ? $_POST["overlay_id"] : "";
		$overlays    = require __DIR__ . "/../../config/overlays.php";
		if (!isset($overlays[$submittedId])) {
			$this->jsonError(400, "Unknown overlay.");
		}
		$overlay = $overlays[$submittedId];

		$file = $_FILES["snap"] ?? null;
		if (!is_array($file)) {
			$this->jsonError(400, "No snap submitted.");
		}

		$validationError = Validator::validateSnapUpload($file);
		if ($validationError !== null) {
			$this->jsonError(400, $validationError);
		}

		$overlayAbsPath = __DIR__ . "/../../public" . $overlay["path"];

		$composer = new ImageComposer();
		$fileName = $composer->merge($file["tmp_name"], $overlayAbsPath, self::SNAPS_DIR);
		if ($fileName === null) {
			$this->jsonError(500, "Failed to compose snap.");
		}

		$publicPath = self::SNAPS_PUBLIC_PREFIX . $fileName;
		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo = (new Database($dbConfig))->connection();
		$imageId = (new Image($pdo))->create((int) Auth::id(), $publicPath, $overlay["id"]);

		$this->jsonSuccess([
			"image_id"   => $imageId,
			"image_path" => $publicPath,
			"overlay_id" => $overlay["id"],
		]);
	}

	public function delete(): void
	{
		Auth::requireAuth();

		$submittedToken = is_string($_POST[Csrf::fieldName()] ?? null)
			? $_POST[Csrf::fieldName()]
			: "";
		if (!Csrf::validate($submittedToken)) {
			$this->jsonError(403, "Invalid CSRF token.");
		}

		$imageId = (int) ($_POST["image_id"] ?? 0);
		if ($imageId <= 0) {
			$this->jsonError(400, "Missing image id.");
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo = (new Database($dbConfig))->connection();
		$images = new Image($pdo);

		$image = $images->findById($imageId);
		if ($image === null) {
			$this->jsonError(404, "Image not found.");
		}
		if ((int) $image["user_id"] !== (int) Auth::id()) {
			$this->jsonError(403, "Forbidden.");
		}

		$absPath = __DIR__ . "/../../public" . $image["image_path"];
		if (is_file($absPath) && !@unlink($absPath)) {
			$this->jsonError(500, "Could not delete file.");
		}

		if (!$images->delete($imageId)) {
			$this->jsonError(500, "Could not delete record.");
		}

		$this->jsonSuccess(["image_id" => $imageId]);
	}

	private function jsonError(int $status, string $message): void
	{
		http_response_code($status);
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode(["ok" => false, "error" => $message], JSON_THROW_ON_ERROR);
		exit;
	}

	private function jsonSuccess(array $payload = []): void
	{
		http_response_code(200);
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode(["ok" => true] + $payload, JSON_THROW_ON_ERROR);
		exit;
	}
}
