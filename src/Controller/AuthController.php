<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Csrf;
use App\Core\Database;
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

	private function renderRegister(array $errors, array $old): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/register", [
			"title"  => "Sign up",
			"errors" => $errors,
			"old"    => $old,
		]);
	}
}
