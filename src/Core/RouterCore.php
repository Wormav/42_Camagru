<?php

declare(strict_types=1);

namespace App\Core;

class RouterCore
{
	private array $routes = ["GET" => [], "POST" => []];

	public function get(string $path, array|callable $handler): self
	{
		$this->routes["GET"][$path] = $handler;
		return $this;
	}

	public function post(string $path, array|callable $handler): self
	{
		$this->routes["POST"][$path] = $handler;
		return $this;
	}

	// dispatch is not responsible for parsing query parameters, it only matches the path
	public function dispatch(string $method, string $uri): void
	{
		$path = parse_url($uri, PHP_URL_PATH) ?: "/";
		if ($path !== "/") {
			$path = rtrim($path, "/");
		}

		if (!isset($this->routes[$method])) {
			http_response_code(405);
			echo "405 Method Not Allowed";
			return;
		}

		if (!isset($this->routes[$method][$path])) {
			http_response_code(404);
			echo "404 Not Found";
			return;
		}

		$handler = $this->routes[$method][$path];

		if (!is_array($handler)) {
			$handler();
			return;
		}

		[$class, $action] = $handler;
		new $class()->$action();
	}
}
