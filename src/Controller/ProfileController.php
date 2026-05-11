<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Session;
use App\Core\Validator;
use App\Model\User;
use App\View\View;

class ProfileController
{
	private const AVATAR_DIR           = __DIR__ . "/../../public/uploads/avatars";
	private const AVATAR_PUBLIC_PREFIX = "/uploads/avatars/";

	private const AVATAR_MIME_TO_EXT = [
		"image/jpeg" => "jpg",
		"image/png"  => "png",
		"image/webp" => "webp",
	];

	public function showProfile(): void
	{
		Auth::requireAuth();

		[, $currentUser] = $this->loadCurrentUser();

		$dbAvatar = is_string($currentUser["avatar_path"] ?? null) ? $currentUser["avatar_path"] : null;
		if (Session::get("avatar_path") !== $dbAvatar) {
			Session::set("avatar_path", $dbAvatar);
		}
		if (Session::get("username") !== $currentUser["username"]) {
			Session::set("username", $currentUser["username"]);
		}

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("profile/profile", [
			"title"       => "Profile",
			"currentUser" => $currentUser,
		]);
	}

	public function updateUsername(): void
	{
		[$users, $currentUser] = $this->guard();

		$newUsername = is_string($_POST["username"] ?? null) ? trim($_POST["username"]) : "";

		$error = Validator::validateUsername($newUsername);
		if ($error !== null) {
			Flash::error($error);
			$this->redirectToProfile();
		}

		if ($newUsername === $currentUser["username"]) {
			$this->redirectToProfile();
		}

		if ($users->findByUsername($newUsername) !== null) {
			Flash::error("This username is already taken.");
			$this->redirectToProfile();
		}

		$users->updateUsername((int) $currentUser["id"], $newUsername);
		Session::set("username", $newUsername);

		Flash::success("Username updated.");
		$this->redirectToProfile();
	}

	public function updateEmail(): void
	{
		[$users, $currentUser] = $this->guard();

		$newEmail = is_string($_POST["email"] ?? null) ? trim($_POST["email"]) : "";

		$error = Validator::validateEmail($newEmail);
		if ($error !== null) {
			Flash::error($error);
			$this->redirectToProfile();
		}

		if ($newEmail === $currentUser["email"]) {
			$this->redirectToProfile();
		}

		if ($users->findByEmail($newEmail) !== null) {
			Flash::error("This email is already taken.");
			$this->redirectToProfile();
		}

		$users->updateEmail((int) $currentUser["id"], $newEmail);

		Flash::success("Email updated.");
		$this->redirectToProfile();
	}

	public function updatePassword(): void
	{
		[$users, $currentUser] = $this->guard();

		$current = is_string($_POST["current_password"] ?? null) ? $_POST["current_password"] : "";
		$new     = is_string($_POST["new_password"] ?? null) ? $_POST["new_password"] : "";

		if ($current === "" || !password_verify($current, $currentUser["password"])) {
			Flash::error("Your current password is incorrect.");
			$this->redirectToProfile();
		}

		$error = Validator::validatePassword($new);
		if ($error !== null) {
			Flash::error($error);
			$this->redirectToProfile();
		}

		if (password_verify($new, $currentUser["password"])) {
			Flash::error("New password must be different from the current one.");
			$this->redirectToProfile();
		}

		$hash = password_hash($new, PASSWORD_BCRYPT);
		$users->updatePassword((int) $currentUser["id"], $hash);

		Flash::success("Password updated.");
		$this->redirectToProfile();
	}

	public function updateNotifications(): void
	{
		[$users, $currentUser] = $this->guard();

		$notify = isset($_POST["notify_comments"]) && $_POST["notify_comments"] === "1";
		$users->updateNotifyComments((int) $currentUser["id"], $notify);

		Flash::success($notify
			? "Comment notifications enabled."
			: "Comment notifications disabled.");
		$this->redirectToProfile();
	}

	public function updateAvatar(): void
	{
		[$users, $currentUser] = $this->guard();

		$file = $_FILES["avatar"] ?? null;
		if (!is_array($file)) {
			Flash::error("No avatar file submitted.");
			$this->redirectToProfile();
		}

		$validationError = Validator::validateAvatarUpload($file);
		if ($validationError !== null) {
			Flash::error($validationError);
			$this->redirectToProfile();
		}

		$tmpPath = $file["tmp_name"];
		$mime    = mime_content_type($tmpPath);
		if ($mime === false || !isset(self::AVATAR_MIME_TO_EXT[$mime])) {
			Flash::error("Avatar must be a JPEG, PNG or WebP image.");
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
			Flash::error("Could not save your avatar. Please try again.");
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
		Session::set("avatar_path", $publicPath);

		Flash::success("Avatar updated.");
		$this->redirectToProfile();
	}

	private function guard(): array
	{
		Auth::requireAuth();

		$submittedToken = is_string($_POST[Csrf::fieldName()] ?? null)
			? $_POST[Csrf::fieldName()]
			: "";
		if (!Csrf::validate($submittedToken)) {
			http_response_code(403);
			echo "Forbidden";
			exit;
		}

		return $this->loadCurrentUser();
	}

	private function loadCurrentUser(): array
	{
		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new Database($dbConfig))->connection();
		$users    = new User($pdo);

		$currentUser = $users->findById((int) Auth::id());
		if ($currentUser === null) {
			Session::destroy();
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
