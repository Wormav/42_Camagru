<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
	public static function validateEmail(string $email): ?string
	{
		if ($email === "") {
			return "Email is required.";
		}
		if (strlen($email) > 255) {
			return "Email is too long (max 255 characters).";
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return "Email format is invalid.";
		}
		return null;
	}

	public static function validateUsername(string $username): ?string
	{
		$length = strlen($username);
		if ($length === 0) {
			return "Username is required.";
		}
		if ($length < 3 || $length > 20) {
			return "Username must be 3 to 20 characters.";
		}
		if (!preg_match("/^[A-Za-z0-9_]+$/", $username)) {
			return "Username can only contain letters, digits and underscore.";
		}
		return null;
	}

	public static function validatePassword(string $password): ?string
	{
		if (strlen($password) < 8) {
			return "Password must be at least 8 characters.";
		}
		if (!preg_match("/[A-Z]/", $password)) {
			return "Password must contain at least one uppercase letter.";
		}
		if (!preg_match("/[a-z]/", $password)) {
			return "Password must contain at least one lowercase letter.";
		}
		if (!preg_match("/\d/", $password)) {
			return "Password must contain at least one digit.";
		}
		if (!preg_match("/[^A-Za-z0-9]/", $password)) {
			return "Password must contain at least one special character.";
		}
		return null;
	}

	public static function validatePasswordConfirmation(string $password, string $confirmation): ?string
	{
		if ($password !== $confirmation) {
			return "Passwords do not match.";
		}
		return null;
	}

	public static function validatePasswordReset(
		string $password,
		string $passwordConfirmation,
	): array {
		$errors = [];
		if ($error = self::validatePassword($password)) {
			$errors[] = $error;
		}
		if ($error = self::validatePasswordConfirmation($password, $passwordConfirmation)) {
			$errors[] = $error;
		}
		return $errors;
	}

	public static function validateRegistration(
		string $email,
		string $username,
		string $password,
		string $passwordConfirmation,
	): array {
		$errors = [];
		if ($error = self::validateEmail($email)) {
			$errors[] = $error;
		}
		if ($error = self::validateUsername($username)) {
			$errors[] = $error;
		}
		if ($error = self::validatePassword($password)) {
			$errors[] = $error;
		}
		if ($error = self::validatePasswordConfirmation($password, $passwordConfirmation)) {
			$errors[] = $error;
		}
		return $errors;
	}
}
