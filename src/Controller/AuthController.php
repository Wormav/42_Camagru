<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;
use App\Model\User;
use App\Service\Mailer;
use App\View\View;
use Throwable;

class AuthController
{
	public function showRegister(): void
	{
		$this->renderRegister([], []);
	}

	public function register(): void
	{
		$submittedToken = is_string($_POST[Csrf::fieldName()] ?? null)
			? $_POST[Csrf::fieldName()]
			: "";
		if (!Csrf::validate($submittedToken)) {
			http_response_code(403);
			echo "Forbidden";
			return;
		}

		$email                = is_string($_POST["email"] ?? null) ? trim($_POST["email"]) : "";
		$username             = is_string($_POST["username"] ?? null) ? trim($_POST["username"]) : "";
		$password             = is_string($_POST["password"] ?? null) ? $_POST["password"] : "";
		$passwordConfirmation = is_string($_POST["password_confirmation"] ?? null) ? $_POST["password_confirmation"] : "";

		$old = [
			"email"    => $email,
			"username" => $username,
		];

		$errors = Validator::validateRegistration($email, $username, $password, $passwordConfirmation);
		if (!empty($errors)) {
			$this->renderRegister($errors, $old);
			return;
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new Database($dbConfig))->connection();
		$users    = new User($pdo);

		if ($users->findByEmail($email) !== null || $users->findByUsername($username) !== null) {
			$this->renderRegister(
				["Sign-up failed. Please review your information."],
				$old,
			);
			return;
		}

		$passwordHash      = password_hash($password, PASSWORD_BCRYPT);
		$verificationToken = bin2hex(random_bytes(32));

		$mailConfig = require __DIR__ . "/../../config/mail.php";
		$mailer     = new Mailer($mailConfig);

		$pdo->beginTransaction();
		try {
			$users->create($email, $username, $passwordHash, $verificationToken);
			$mailer->sendVerification($email, $username, $verificationToken);
			$pdo->commit();
		} catch (Throwable $error) {
			$pdo->rollBack();
			error_log("Registration failed for {$email}: " . $error->getMessage());
			$this->renderRegister(
				["Sign-up failed. Please try again in a moment."],
				$old,
			);
			return;
		}

		header("Location: /register/check-email");
		exit;
	}

	public function showCheckEmail(): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/check_email", [
			"title" => "Check your inbox",
		]);
	}

	public function verify(): void
	{
		$token = is_string($_GET["token"] ?? null) ? trim($_GET["token"]) : "";

		$looksValid = $token !== ""
			&& strlen($token) === 64
			&& ctype_xdigit($token);

		$success = false;
		if ($looksValid) {
			$dbConfig = require __DIR__ . "/../../config/database.php";
			$pdo      = (new Database($dbConfig))->connection();
			$users    = new User($pdo);
			$success  = $users->verifyByToken($token);
		}

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/verify", [
			"title"   => $success ? "Account verified" : "Verification failed",
			"success" => $success,
		]);
	}

	private function renderRegister(array $errors, array $old): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/register", [
			"title"  => "Sign up",
			"errors" => $errors,
			"old"    => $old,
		]);
	}

	public function showLogin(): void
	{
		Auth::requireGuest();
		$this->renderLogin([], []);
	}

	public function login(): void
	{
		$submittedToken = is_string($_POST[Csrf::fieldName()] ?? null)
			? $_POST[Csrf::fieldName()]
			: "";
		if (!Csrf::validate($submittedToken)) {
			http_response_code(403);
			echo "Forbidden";
			return;
		}

		$username = is_string($_POST["username"] ?? null) ? trim($_POST["username"]) : "";
		$password = is_string($_POST["password"] ?? null) ? $_POST["password"] : "";

		$old = ["username" => $username];

		if ($username === "" || $password === "") {
			$this->renderLogin(["Invalid credentials."], $old);
			return;
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new Database($dbConfig))->connection();
		$users    = new User($pdo);

		$user = $users->findByUsername($username);

		if (
			$user === null
			|| !password_verify($password, $user["password"])
			|| (int) $user["is_verified"] !== 1
		) {
			$this->renderLogin(["Invalid credentials."], $old);
			return;
		}

		Session::regenerate();
		Session::set("user_id", (int) $user["id"]);
		Session::set("username", $user["username"]);

		header("Location: /");
		exit;
	}

	public function logout(): void
	{
		$submittedToken = is_string($_POST[Csrf::fieldName()] ?? null)
			? $_POST[Csrf::fieldName()]
			: "";
		if (!Csrf::validate($submittedToken)) {
			http_response_code(403);
			echo "Forbidden";
			return;
		}

		Session::destroy();

		header("Location: /");
		exit;
	}

	private function renderLogin(array $errors, array $old): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/login", [
			"title"  => "Sign in",
			"errors" => $errors,
			"old"    => $old,
		]);
	}

	public function showForgotPassword(): void
	{
		Auth::requireGuest();
		$this->renderForgotPassword([], []);
	}

	public function forgotPassword(): void
	{
		Auth::requireGuest();

		$submittedToken = is_string($_POST[Csrf::fieldName()] ?? null)
			? $_POST[Csrf::fieldName()]
			: "";
		if (!Csrf::validate($submittedToken)) {
			http_response_code(403);
			echo "Forbidden";
			return;
		}

		$email = is_string($_POST["email"] ?? null) ? trim($_POST["email"]) : "";
		$old   = ["email" => $email];

		$error = Validator::validateEmail($email);
		if ($error !== null) {
			$this->renderForgotPassword([$error], $old);
			return;
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new Database($dbConfig))->connection();
		$users    = new User($pdo);

		// Only send a reset email to verified accounts. We do not leak that
		// the address is unknown — the success page is always the same.
		$user = $users->findByEmail($email);
		if ($user !== null && (int) $user["is_verified"] === 1) {
			$token     = bin2hex(random_bytes(32));
			$expiresAt = (new \DateTimeImmutable("+1 hour"))->format("Y-m-d H:i:s");

			$mailConfig = require __DIR__ . "/../../config/mail.php";
			$mailer     = new Mailer($mailConfig);

			try {
				$users->setResetToken((int) $user["id"], $token, $expiresAt);
				$mailer->sendPasswordReset($email, $user["username"], $token);
			} catch (Throwable $error) {
				// Swallow errors here on purpose — we keep the generic success
				// response to avoid email enumeration.
				error_log("Password reset email failed for {$email}: " . $error->getMessage());
			}
		}

		header("Location: /reset/sent");
		exit;
	}

	public function showForgotPasswordSent(): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/forgot_sent", [
			"title" => "Check your inbox",
		]);
	}

	public function showResetPassword(): void
	{
		Auth::requireGuest();

		$token = is_string($_GET["token"] ?? null) ? trim($_GET["token"]) : "";

		$looksValid = $token !== ""
			&& strlen($token) === 64
			&& ctype_xdigit($token);

		if (!$looksValid) {
			$this->renderResetInvalid();
			return;
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new Database($dbConfig))->connection();
		$users    = new User($pdo);

		if ($users->findByValidResetToken($token) === null) {
			$this->renderResetInvalid();
			return;
		}

		$this->renderResetForm($token, [], []);
	}

	public function resetPassword(): void
	{
		Auth::requireGuest();

		$submittedToken = is_string($_POST[Csrf::fieldName()] ?? null)
			? $_POST[Csrf::fieldName()]
			: "";
		if (!Csrf::validate($submittedToken)) {
			http_response_code(403);
			echo "Forbidden";
			return;
		}

		$token                = is_string($_POST["token"] ?? null) ? trim($_POST["token"]) : "";
		$password             = is_string($_POST["password"] ?? null) ? $_POST["password"] : "";
		$passwordConfirmation = is_string($_POST["password_confirmation"] ?? null) ? $_POST["password_confirmation"] : "";

		$looksValid = $token !== ""
			&& strlen($token) === 64
			&& ctype_xdigit($token);

		if (!$looksValid) {
			$this->renderResetInvalid();
			return;
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new Database($dbConfig))->connection();
		$users    = new User($pdo);

		$user = $users->findByValidResetToken($token);
		if ($user === null) {
			$this->renderResetInvalid();
			return;
		}

		$errors = Validator::validatePasswordReset($password, $passwordConfirmation);
		if (!empty($errors)) {
			$this->renderResetForm($token, $errors, []);
			return;
		}

		$passwordHash = password_hash($password, PASSWORD_BCRYPT);
		$users->updatePasswordAndClearReset((int) $user["id"], $passwordHash);

		header("Location: /login");
		exit;
	}

	private function renderForgotPassword(array $errors, array $old): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/forgot", [
			"title"  => "Reset your password",
			"errors" => $errors,
			"old"    => $old,
		]);
	}

	private function renderResetForm(string $token, array $errors, array $old): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/reset", [
			"title"  => "Choose a new password",
			"token"  => $token,
			"errors" => $errors,
			"old"    => $old,
		]);
	}

	private function renderResetInvalid(): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/reset_invalid", [
			"title" => "Reset link invalid",
		]);
	}
}
