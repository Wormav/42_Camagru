<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Validator;
use App\Model\User;
use App\View\View;

class AuthController
{
	/**
	 * GET /register — render the empty sign-up form.
	 */
	public function showRegister(): void
	{
		$this->renderRegister([], []);
	}

	/**
	 * POST /register — validate, persist, then redirect to the post-signup
	 * confirmation page. The verification email is sent here too (TODO,
	 * step 5: hook the Mailer once it exists).
	 */
	public function register(): void
	{
		// 1. CSRF check — the only thing we do before reading any input.
		$submittedToken = is_string($_POST[Csrf::fieldName()] ?? null)
			? $_POST[Csrf::fieldName()]
			: "";
		if (!Csrf::validate($submittedToken)) {
			http_response_code(403);
			echo "Forbidden";
			return;
		}

		// 2. Pull and normalise the inputs. Trim only the user-facing fields;
		//    passwords are kept verbatim — leading/trailing spaces are valid.
		$email                = is_string($_POST["email"] ?? null)                 ? trim($_POST["email"])    : "";
		$username             = is_string($_POST["username"] ?? null)              ? trim($_POST["username"]) : "";
		$password             = is_string($_POST["password"] ?? null)              ? $_POST["password"]       : "";
		$passwordConfirmation = is_string($_POST["password_confirmation"] ?? null) ? $_POST["password_confirmation"] : "";

		// "Old" values are echoed back into the form on failure (no passwords).
		$old = [
			"email"    => $email,
			"username" => $username,
		];

		// 3. Format-level validation (length, charset, regex…). These messages
		//    are detailed since they leak no sensitive info.
		$errors = Validator::validateRegistration($email, $username, $password, $passwordConfirmation);
		if (!empty($errors)) {
			$this->renderRegister($errors, $old);
			return;
		}

		// 4. Existence-level validation (email/username uniqueness). To avoid
		//    user enumeration we use a single vague message.
		$config = require __DIR__ . "/../../config/database.php";
		$pdo    = (new Database($config))->connection();
		$users  = new User($pdo);

		if ($users->findByEmail($email) !== null || $users->findByUsername($username) !== null) {
			$this->renderRegister(
				["Sign-up failed. Please review your information."],
				$old,
			);
			return;
		}

		// 5. Hash the password and mint a one-time verification token.
		$passwordHash      = password_hash($password, PASSWORD_BCRYPT);
		$verificationToken = bin2hex(random_bytes(32));

		// 6. Persist the user.
		$users->create($email, $username, $passwordHash, $verificationToken);

		// 7. TODO(JLO-7 step 5): send the verification email here, e.g.:
		//    Mailer::sendVerification($email, $username, $verificationToken);

		// 8. Redirect to a "check your email" page (PRG pattern: avoids form
		//    re-submission on refresh).
		header("Location: /register/check-email");
		exit;
	}

	/**
	 * GET /register/check-email — confirmation page after a successful signup.
	 */
	public function showCheckEmail(): void
	{
		$view = new View(__DIR__ . "/../View/templates");
		$view->render("auth/check_email", [
			"title" => "Check your inbox",
		]);
	}

	/**
	 * Shared rendering helper used by both GET and the failure branches of
	 * POST /register, so the error-feedback path lives in one place.
	 *
	 * @param list<string>            $errors
	 * @param array<string, string>   $old
	 */
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
