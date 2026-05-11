<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Validator;
use App\View\View;

class PostController
{
	public function showPost(): void
	{
		Auth::requireAuth();

		$overlays = require __DIR__ . "/../../config/overlays.php";

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("post/post", [
			"title"    => "Post",
			"scripts"  => ["/js/post.js"],
			"overlays" => $overlays,
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

		$this->jsonSuccess([
			"message"    => "Snap received.",
			"overlay_id" => $overlay["id"],
		]);
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
