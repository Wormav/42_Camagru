<?php
/**
 * Default application layout — Neo-brutalist aesthetic.
 *
 * @var \Closure(mixed): string $e        Escape helper.
 * @var string                  $content  Page HTML rendered by App\View\View.
 * @var string|null             $title    Optional page title.
 */

$navItems = [
	["label" => "Gallery", "href" => "/gallery"],
	["label" => "Edit",    "href" => "/edit"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="theme-color" content="#FFF8E1">
	<title><?= $e($title ?? "Camagru") ?></title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="/style.css">
</head>
<body class="min-h-screen flex flex-col">

<!-- Top navigation -->
<header class="relative z-10 border-b-3 border-ink bg-paper">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-6">

		<!-- Brand -->
		<a href="/" class="flex items-center gap-3 group">
			<span class="tile bg-lime group-hover:bg-pink transition-colors">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6" aria-hidden="true">
					<rect x="3" y="6" width="18" height="13" rx="0"/>
					<circle cx="12" cy="12.5" r="3.5"/>
					<path d="M8 6l1.5-2h5L16 6"/>
				</svg>
			</span>
			<span class="font-display font-black text-2xl sm:text-3xl tracking-tight">Camagru</span>
		</a>

		<!-- Center nav -->
		<nav class="hidden md:flex items-center gap-2">
			<?php foreach ($navItems as $item): ?>
				<a href="<?= $e($item["href"]) ?>" class="px-3 py-1.5 font-display font-bold text-sm uppercase border-3 border-transparent hover:border-ink hover:bg-cyan transition-all">
					<?= $e($item["label"]) ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<!-- Right actions -->
		<div class="flex items-center gap-3">
			<a href="/login" class="hidden sm:inline-block font-display font-bold text-sm uppercase underline decoration-3 underline-offset-4 hover:bg-lime px-2 py-1 transition-colors">
				Sign in
			</a>
			<a href="/register" class="btn-brutal !py-2 !px-3 text-sm">
				Sign up →
			</a>
		</div>
	</div>
</header>

<!-- Page content -->
<main class="relative z-10 flex-1 w-full">
	<?= $content ?>
</main>

<!-- Footer — soft-dark brutalist block, four-column meta. -->
<footer class="relative z-10 border-t-3 border-ink bg-dark text-paper mt-24">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 grid grid-cols-2 md:grid-cols-12 gap-8">

		<!-- Brand column -->
		<div class="col-span-2 md:col-span-5">
			<div class="flex items-center gap-3">
				<span class="tile bg-lime !shadow-none">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-ink" aria-hidden="true">
						<rect x="3" y="6" width="18" height="13" rx="0"/>
						<circle cx="12" cy="12.5" r="3.5"/>
						<path d="M8 6l1.5-2h5L16 6"/>
					</svg>
				</span>
				<span class="font-display font-black text-2xl sm:text-3xl tracking-tight">Camagru</span>
			</div>
			<p class="mt-4 text-sm leading-relaxed max-w-sm text-paper/70">
				Browser-based photobooth with server-side image merging. Built with pure PHP, no framework.
			</p>
		</div>

		<!-- Product -->
		<div class="col-span-1 md:col-span-2">
			<p class="font-display font-black text-sm uppercase mb-3">Product</p>
			<ul class="space-y-2 text-sm font-medium text-paper/70">
				<li><a href="/gallery" class="hover:text-lime hover:underline decoration-3 underline-offset-4 transition-colors">Gallery</a></li>
				<li><a href="/edit" class="hover:text-lime hover:underline decoration-3 underline-offset-4 transition-colors">Edit</a></li>
				<li><a href="/register" class="hover:text-lime hover:underline decoration-3 underline-offset-4 transition-colors">Sign up</a></li>
			</ul>
		</div>

		<!-- Account -->
		<div class="col-span-1 md:col-span-2">
			<p class="font-display font-black text-sm uppercase mb-3">Account</p>
			<ul class="space-y-2 text-sm font-medium text-paper/70">
				<li><a href="/login" class="hover:text-lime hover:underline decoration-3 underline-offset-4 transition-colors">Sign in</a></li>
				<li><a href="/profile" class="hover:text-lime hover:underline decoration-3 underline-offset-4 transition-colors">Profile</a></li>
				<li><a href="/reset" class="hover:text-lime hover:underline decoration-3 underline-offset-4 transition-colors">Reset password</a></li>
			</ul>
		</div>

		<!-- Stack -->
		<div class="col-span-2 md:col-span-3">
			<p class="font-display font-black text-sm uppercase mb-3">Stack</p>
			<ul class="space-y-2 text-sm font-mono text-paper/70">
				<li>PHP 8.1 + GD</li>
				<li>MySQL 8.0 + PDO</li>
				<li>Tailwind CLI</li>
				<li>Docker Compose</li>
			</ul>
		</div>
	</div>

	<!-- Bottom strip -->
	<div class="border-t-3 border-paper/20">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs font-mono uppercase text-paper/60">
			<p>© <?= date("Y") ?> Camagru — All rights reserved</p>
			<p>v4.1 · 42 Paris</p>
		</div>
	</div>
</footer>

</body>
</html>
