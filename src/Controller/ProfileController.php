<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\AuthCore;
use App\Core\CsrfCore;
use App\Core\DatabaseCore;
use App\Core\FlashCore;
use App\Core\SessionCore;
use App\Core\ValidatorCore;
use App\Model\UserModel;
use App\View\View;

class ProfileController
{
	private const AVATAR_DIR           = __DIR__ . "/../../uploads/avatars";
	private const AVATAR_PUBLIC_PREFIX = "/uploads/avatars/";

	private const AVATAR_MIME_TO_EXT = [
		"image/jpeg" => "jpg",
		"image/png"  => "png",
		"image/webp" => "webp",
	];

	public function showProfile(): void
	{
		AuthCore::requireAuth();

		[, $currentUser] = $this->loadCurrentUser();

		$dbAvatar = is_string($currentUser["avatar_path"] ?? null) ? $currentUser["avatar_path"] : null;
		if (SessionCore::get("avatar_path") !== $dbAvatar) {
			SessionCore::set("avatar_path", $dbAvatar);
		}
		if (SessionCore::get("username") !== $currentUser["username"]) {
			SessionCore::set("username", $currentUser["username"]);
		}

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("profile/profileTemplate", [
			"title"       => "Profile",
			"currentUser" => $currentUser,
		]);
	}

	public function updateUsername(): void
	{
		[$users, $currentUser] = $this->guard();

		$newUsername = is_string($_POST["username"] ?? null) ? trim($_POST["username"]) : "";

		$error = ValidatorCore::validateUsername($newUsername);
		if ($error !== null) {
			FlashCore::error($error);
			$this->redirectToProfile();
		}

		if ($newUsername === $currentUser["username"]) {
			$this->redirectToProfile();
		}

		if ($users->findByUsername($newUsername) !== null) {
			FlashCore::error("This username is already taken.");
			$this->redirectToProfile();
		}

		$users->updateUsername((int) $currentUser["id"], $newUsername);
		SessionCore::set("username", $newUsername);

		FlashCore::success("Username updated.");
		$this->redirectToProfile();
	}

	public function updateEmail(): void
	{
		[$users, $currentUser] = $this->guard();

		$newEmail = is_string($_POST["email"] ?? null) ? trim($_POST["email"]) : "";

		$error = ValidatorCore::validateEmail($newEmail);
		if ($error !== null) {
			FlashCore::error($error);
			$this->redirectToProfile();
		}

		if ($newEmail === $currentUser["email"]) {
			$this->redirectToProfile();
		}

		if ($users->findByEmail($newEmail) !== null) {
			FlashCore::error("This email is already taken.");
			$this->redirectToProfile();
		}

		$users->updateEmail((int) $currentUser["id"], $newEmail);

		FlashCore::success("Email updated.");
		$this->redirectToProfile();
	}

	public function updatePassword(): void
	{
		[$users, $currentUser] = $this->guard();

		$current = is_string($_POST["current_password"] ?? null) ? $_POST["current_password"] : "";
		$new     = is_string($_POST["new_password"] ?? null) ? $_POST["new_password"] : "";

		if ($current === "" || !password_verify($current, $currentUser["password"])) {
			FlashCore::error("Your current password is incorrect.");
			$this->redirectToProfile();
		}

		$error = ValidatorCore::validatePassword($new);
		if ($error !== null) {
			FlashCore::error($error);
			$this->redirectToProfile();
		}

		if (password_verify($new, $currentUser["password"])) {
			FlashCore::error("New password must be different from the current one.");
			$this->redirectToProfile();
		}

		$hash = password_hash($new, PASSWORD_BCRYPT);
		$users->updatePassword((int) $currentUser["id"], $hash);

		FlashCore::success("Password updated.");
		$this->redirectToProfile();
	}

	public function updateNotifications(): void
	{
		[$users, $currentUser] = $this->guard();

		$notify = isset($_POST["notify_comments"]) && $_POST["notify_comments"] === "1";
		$users->updateNotifyComments((int) $currentUser["id"], $notify);

		FlashCore::success($notify
			? "Comment notifications enabled."
			: "Comment notifications disabled.");
		$this->redirectToProfile();
	}

	public function updateAvatar(): void
	{
		[$users, $currentUser] = $this->guard();

		$file = $_FILES["avatar"] ?? null;
		if (!is_array($file)) {
			FlashCore::error("No avatar file submitted.");
			$this->redirectToProfile();
		}

		$validationError = ValidatorCore::validateAvatarUpload($file);
		if ($validationError !== null) {
			FlashCore::error($validationError);
			$this->redirectToProfile();
		}

		$tmpPath = $file["tmp_name"];
		$mime    = mime_content_type($tmpPath);
		if ($mime === false || !isset(self::AVATAR_MIME_TO_EXT[$mime])) {
			FlashCore::error("Avatar must be a JPEG, PNG or WebP image.");
			$this->redirectToProfile();
		}

		$extension    = self::AVATAR_MIME_TO_EXT[$mime];
		$filename     = bin2hex(random_bytes(16)) . "." . $extension;
		$absolutePath = self::AVATAR_DIR . "/" . $filename;
		$publicPath   = self::AVATAR_PUBLIC_PREFIX . $filename;

		if (!is_dir(self::AVATAR_DIR)) {
			mkdir(self::AVATAR_DIR, 0775, true);
		}

		if (!move_uploaded_file($tmpPath, $absolutePath)) {
			FlashCore::error("Could not save your avatar. Please try again.");
			$this->redirectToProfile();
		}

		$previous = is_string($currentUser["avatar_path"] ?? null) ? $currentUser["avatar_path"] : "";
		if ($previous !== "" && str_starts_with($previous, self::AVATAR_PUBLIC_PREFIX)) {
			$previousAbsolute = self::AVATAR_DIR . "/" . basename($previous);
			if (is_file($previousAbsolute)) {
				@unlink($previousAbsolute);
			}
		}

		$users->updateAvatar((int) $currentUser["id"], $publicPath);
		SessionCore::set("avatar_path", $publicPath);

		FlashCore::success("Avatar updated.");
		$this->redirectToProfile();
	}

	private function guard(): array
	{
		AuthCore::requireAuth();

		$submittedToken = is_string($_POST[CsrfCore::fieldName()] ?? null)
			? $_POST[CsrfCore::fieldName()]
			: "";
		if (!CsrfCore::validate($submittedToken)) {
			http_response_code(403);
			echo "Forbidden";
			exit;
		}

		return $this->loadCurrentUser();
	}

	private function loadCurrentUser(): array
	{
		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new DatabaseCore($dbConfig))->connection();
		$users    = new UserModel($pdo);

		$currentUser = $users->findById((int) AuthCore::id());
		if ($currentUser === null) {
			SessionCore::destroy();
			header("Location: /login");
			exit;
		}

		return [$users, $currentUser];
	}

	private function redirectToProfile(): never
	{
		header("Location: /profile");
		exit;
	}
}
