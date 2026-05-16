<?php

declare(strict_types=1);

namespace App\Service;

use RuntimeException;

class MailerService
{
	public function __construct(private array $config)
	{
	}

	public function sendVerification(string $email, string $username, string $token): bool
	{
		$link    = $this->buildUrl("/verify", ["token" => $token]);
		$subject = "Verify your Camagru account";

		$body  = "Hello {$username},\n\n";
		$body .= "Thanks for signing up to Camagru.\n";
		$body .= "Click the link below to verify your email address:\n\n";
		$body .= $link . "\n\n";
		$body .= "If you didn't create this account you can safely ignore this email.\n\n";
		$body .= "— The Camagru Team";

		return $this->send($email, $subject, $body);
	}

	public function sendCommentNotification(
		string $authorEmail,
		string $authorUsername,
		string $commenterUsername,
		string $commentContent,
		int $imageId
	): bool {
		$link    = $this->buildUrl("/gallery", ["image" => $imageId]);
		$subject = "{$commenterUsername} commented on your snap";

		$body  = "Hello {$authorUsername},\n\n";
		$body .= "{$commenterUsername} just commented on one of your snaps:\n\n";
		$body .= "\"{$commentContent}\"\n\n";
		$body .= "See it on Camagru:\n";
		$body .= $link . "\n\n";
		$body .= "You can disable these notifications from your profile preferences.\n\n";
		$body .= "— The Camagru Team";

		return $this->send($authorEmail, $subject, $body);
	}

	public function sendPasswordReset(string $email, string $username, string $token): bool
	{
		$link    = $this->buildUrl("/reset/confirm", ["token" => $token]);
		$subject = "Reset your Camagru password";

		$body  = "Hello {$username},\n\n";
		$body .= "We received a request to reset your password.\n";
		$body .= "Click the link below to pick a new one (valid for 1 hour):\n\n";
		$body .= $link . "\n\n";
		$body .= "If you didn't request this you can safely ignore this email.\n\n";
		$body .= "— The Camagru Team";

		return $this->send($email, $subject, $body);
	}

	private function send(string $to, string $subject, string $body): bool
	{
		$host = $this->config["host"];
		$port = $this->config["port"];

		$socket = @fsockopen($host, $port, $errno, $errstr, 30);
		if ($socket === false) {
			throw new RuntimeException("SMTP connect failed: {$errstr} ({$errno})");
		}
		stream_set_timeout($socket, 30);

		try {
			$this->expect($socket, "220");

			$this->command($socket, "EHLO " . ($_SERVER["HTTP_HOST"] ?? "localhost"));
			$this->expect($socket, "250");

			$this->command($socket, "STARTTLS");
			$this->expect($socket, "220");

			$crypto = stream_socket_enable_crypto(
				$socket,
				true,
				STREAM_CRYPTO_METHOD_TLS_CLIENT,
			);
			if ($crypto !== true) {
				throw new RuntimeException("STARTTLS handshake failed");
			}

			$this->command($socket, "EHLO " . ($_SERVER["HTTP_HOST"] ?? "localhost"));
			$this->expect($socket, "250");

			$this->command($socket, "AUTH LOGIN");
			$this->expect($socket, "334");
			$this->command($socket, base64_encode($this->config["username"]));
			$this->expect($socket, "334");
			$this->command($socket, base64_encode($this->config["password"]));
			$this->expect($socket, "235");

			$this->command($socket, "MAIL FROM: <{$this->config["from"]}>");
			$this->expect($socket, "250");
			$this->command($socket, "RCPT TO: <{$to}>");
			$this->expect($socket, "250");

			$this->command($socket, "DATA");
			$this->expect($socket, "354");

			$headers  = "From: Camagru <{$this->config["from"]}>\r\n";
			$headers .= "To: <{$to}>\r\n";
			$headers .= "Subject: " . $this->encodeHeader($subject) . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
			$headers .= "Content-Transfer-Encoding: 8bit\r\n";
			$headers .= "Date: " . date("r") . "\r\n";

			$safeBody = (string) preg_replace("/^\./m", "..", $body);

			fwrite($socket, $headers . "\r\n" . $safeBody . "\r\n.\r\n");
			$this->expect($socket, "250");

			$this->command($socket, "QUIT");
			return true;
		} finally {
			fclose($socket);
		}
	}


	private function command($socket, string $line): void
	{
		fwrite($socket, $line . "\r\n");
	}


	private function expect($socket, string $code): string
	{
		$response = "";
		while (($line = fgets($socket, 515)) !== false) {
			$response .= $line;

			if (strlen($line) >= 4 && $line[3] === " ") {
				break;
			}
		}
		if (!str_starts_with($response, $code)) {
			throw new RuntimeException("SMTP unexpected reply: " . trim($response));
		}
		return $response;
	}

	private function encodeHeader(string $value): string
	{
		if (preg_match("/[^\x20-\x7e]/", $value) === 1) {
			return "=?UTF-8?B?" . base64_encode($value) . "?=";
		}
		return $value;
	}

	private function buildUrl(string $path, array $query = []): string
	{
		$base = rtrim($this->config["app_url"], "/");
		$qs   = $query !== [] ? "?" . http_build_query($query) : "";
		return $base . $path . $qs;
	}
}
