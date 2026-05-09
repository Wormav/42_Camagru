<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
	private array $routes = ["GET" => [], "POST" => [], "PUT" => [], "DELETE" => []];

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

	public function put(string $path, array|callable $handler): self
	{
		$this->routes["PUT"][$path] = $handler;
		return $this;
	}

	public function delete(string $path, array|callable $handler): self
	{
		$this->routes["DELETE"][$path] = $handler;
		return $this;
	}

	public function dispatch(string $method, string $uri): void
	{
		// HTML forms only support GET/POST natively. Allow PUT/DELETE via a
		// hidden _method field on POST requests.
		if ($method === "POST" && isset($_POST["_method"])) {
			$override = strtoupper((string) $_POST["_method"]);
			if (isset($this->routes[$override])) {
				$method = $override;
			}
		}

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
