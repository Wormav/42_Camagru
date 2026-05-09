<?php
/**
 * Variables exposed by App\View\View::render().
 *
 * @var \Closure(mixed): string $e      Escape helper for outputting variables.
 * @var string|null             $title  Page title shown in the <title> tag.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $e($title ?? "Camagru") ?></title>
	<link rel="stylesheet" href="/style.css">
</head>
<body class="min-h-screen flex flex-col bg-gray-50 text-gray-900 antialiased">

<header class="bg-white border-b border-gray-200 shadow-sm">
	<nav class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
		<a href="/" class="text-xl font-bold tracking-tight text-gray-900 hover:text-indigo-600 transition-colors">
			📸 Camagru
		</a>
		<ul class="flex flex-wrap items-center gap-4 text-sm font-medium text-gray-700">
			<li><a href="/gallery" class="hover:text-indigo-600 transition-colors">Galerie</a></li>
			<li><a href="/edit" class="hover:text-indigo-600 transition-colors">Édition</a></li>
			<li>
				<a href="/login" class="rounded-md bg-indigo-600 px-3 py-1.5 text-white hover:bg-indigo-700 transition-colors">
					Connexion
				</a>
			</li>
		</ul>
	</nav>
</header>

<main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 py-8">
