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
use App\Service\MailerService;
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
		$submittedToken = is_string($_POST[CsrfCore::fieldName()] ?? null)
			? $_POST[CsrfCore::fieldName()]
			: "";
		if (!CsrfCore::validate($submittedToken)) {
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

		$errors = ValidatorCore::validateRegistration($email, $username, $password, $passwordConfirmation);
		if (!empty($errors)) {
			$this->renderRegister($errors, $old);
			return;
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new DatabaseCore($dbConfig))->connection();
		$users    = new UserModel($pdo);

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
		$mailer     = new MailerService($mailConfig);

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
		$view->render("auth/check_emailTemplate", [
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
			$pdo      = (new DatabaseCore($dbConfig))->connection();
			$users    = new UserModel($pdo);
			$success  = $users->verifyByToken($token);
		}

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/verifyTemplate", [
			"title"   => $success ? "Account verified" : "Verification failed",
			"success" => $success,
		]);
	}

	private function renderRegister(array $errors, array $old): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/registerTemplate", [
			"title"  => "Sign up",
			"errors" => $errors,
			"old"    => $old,
		]);
	}

	public function showLogin(): void
	{
		AuthCore::requireGuest();
		$this->renderLogin([], []);
	}

	public function login(): void
	{
		$submittedToken = is_string($_POST[CsrfCore::fieldName()] ?? null)
			? $_POST[CsrfCore::fieldName()]
			: "";
		if (!CsrfCore::validate($submittedToken)) {
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
		$pdo      = (new DatabaseCore($dbConfig))->connection();
		$users    = new UserModel($pdo);

		$user = $users->findByUsername($username);

		if (
			$user === null
			|| !password_verify($password, $user["password"])
			|| (int) $user["is_verified"] !== 1
		) {
			$this->renderLogin(["Invalid credentials."], $old);
			return;
		}

		SessionCore::regenerate();
		SessionCore::set("user_id", (int) $user["id"]);
		SessionCore::set("username", $user["username"]);
		SessionCore::set("avatar_path", $user["avatar_path"] ?? null);

		FlashCore::success("Welcome back, " . $user["username"] . " 👋");

		header("Location: /");
		exit;
	}



	public function logout(): void
	{
		$submittedToken = is_string($_POST[CsrfCore::fieldName()] ?? null)
			? $_POST[CsrfCore::fieldName()]
			: "";
		if (!CsrfCore::validate($submittedToken)) {
			http_response_code(403);
			echo "Forbidden";
			return;
		}

		SessionCore::destroy();

		header("Location: /");
		exit;
	}

	private function renderLogin(array $errors, array $old): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/loginTemplate", [
			"title"  => "Sign in",
			"errors" => $errors,
			"old"    => $old,
		]);
	}

	public function showForgotPassword(): void
	{
		AuthCore::requireGuest();
		$this->renderForgotPassword([], []);
	}

	public function forgotPassword(): void
	{
		AuthCore::requireGuest();

		$submittedToken = is_string($_POST[CsrfCore::fieldName()] ?? null)
			? $_POST[CsrfCore::fieldName()]
			: "";
		if (!CsrfCore::validate($submittedToken)) {
			http_response_code(403);
			echo "Forbidden";
			return;
		}

		$email = is_string($_POST["email"] ?? null) ? trim($_POST["email"]) : "";
		$old   = ["email" => $email];

		$error = ValidatorCore::validateEmail($email);
		if ($error !== null) {
			$this->renderForgotPassword([$error], $old);
			return;
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new DatabaseCore($dbConfig))->connection();
		$users    = new UserModel($pdo);

		$user = $users->findByEmail($email);
		if ($user !== null && (int) $user["is_verified"] === 1) {
			$token     = bin2hex(random_bytes(32));
			$expiresAt = (new \DateTimeImmutable("+1 hour"))->format("Y-m-d H:i:s");

			$mailConfig = require __DIR__ . "/../../config/mail.php";
			$mailer     = new MailerService($mailConfig);

			try {
				$users->setResetToken((int) $user["id"], $token, $expiresAt);
				$mailer->sendPasswordReset($email, $user["username"], $token);
			} catch (Throwable $error) {
				error_log("Password reset email failed for {$email}: " . $error->getMessage());
			}
		}

		header("Location: /reset/sent");
		exit;
	}

	public function showForgotPasswordSent(): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/forgot_sentTemplate", [
			"title" => "Check your inbox",
		]);
	}

	public function showResetPassword(): void
	{
		AuthCore::requireGuest();

		$token = is_string($_GET["token"] ?? null) ? trim($_GET["token"]) : "";

		$looksValid = $token !== ""
			&& strlen($token) === 64
			&& ctype_xdigit($token);

		if (!$looksValid) {
			$this->renderResetInvalid();
			return;
		}

		$dbConfig = require __DIR__ . "/../../config/database.php";
		$pdo      = (new DatabaseCore($dbConfig))->connection();
		$users    = new UserModel($pdo);

		if ($users->findByValidResetToken($token) === null) {
			$this->renderResetInvalid();
			return;
		}

		$this->renderResetForm($token, [], []);
	}

	public function resetPassword(): void
	{
		AuthCore::requireGuest();

		$submittedToken = is_string($_POST[CsrfCore::fieldName()] ?? null)
			? $_POST[CsrfCore::fieldName()]
			: "";
		if (!CsrfCore::validate($submittedToken)) {
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
		$pdo      = (new DatabaseCore($dbConfig))->connection();
		$users    = new UserModel($pdo);

		$user = $users->findByValidResetToken($token);
		if ($user === null) {
			$this->renderResetInvalid();
			return;
		}

		$errors = ValidatorCore::validatePasswordReset($password, $passwordConfirmation);
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
		$view->render("auth/forgotTemplate", [
			"title"  => "Reset your password",
			"errors" => $errors,
			"old"    => $old,
		]);
	}

	private function renderResetForm(string $token, array $errors, array $old): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/resetTemplate", [
			"title"  => "Choose a new password",
			"token"  => $token,
			"errors" => $errors,
			"old"    => $old,
		]);
	}

	private function renderResetInvalid(): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/reset_invalidTemplate", [
			"title" => "Reset link invalid",
		]);
	}
}
