<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\View\View;

class PostController
{
	public function showPost(): void
	{
		Auth::requireAuth();

		$view = new View(__DIR__ . "/../View/templates");
		$view->render("post/post", [
			"title" => "Post",
			 "scripts" => ["/js/post.js"],
		]);
	}
}
