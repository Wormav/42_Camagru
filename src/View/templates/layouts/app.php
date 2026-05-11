<?php
/**
 * Default application layout — Neo-brutalist aesthetic.
 *
 * @var \Closure(mixed): string $e        Escape helper.
 * @var string                  $content  Page HTML rendered by App\View\View.
 * @var string|null             $title    Optional page title.
 */

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;

$navItems = [
	["label" => "Gallery", "href" => "/gallery"],
	["label" => "Edit",    "href" => "/edit"],
];

$isAuth     = Auth::check();
$username   = Auth::username();
$avatarPath = Auth::avatarPath();

$flashSuccess = Flash::get("success");
$flashError   = Flash::get("error");
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
	<script src="/js/toast.js" defer></script>
</head>
<body class="min-h-screen flex flex-col">

<!-- Top navigation -->
<header class="relative z-20 border-b-3 border-ink bg-paper">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">

		<!-- Brand -->
		<a href="/" class="flex items-center gap-3 group shrink-0">
			<span class="tile bg-lime group-hover:bg-pink transition-colors">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6" aria-hidden="true">
					<rect x="3" y="6" width="18" height="13" rx="0"/>
					<circle cx="12" cy="12.5" r="3.5"/>
					<path d="M8 6l1.5-2h5L16 6"/>
				</svg>
			</span>
			<span class="font-display font-black text-2xl sm:text-3xl tracking-tight">Camagru</span>
		</a>

		<!-- Desktop center nav -->
		<nav class="hidden md:flex items-center gap-2">
			<?php foreach ($navItems as $item): ?>
				<a href="<?= $e($item["href"]) ?>" class="px-3 py-1.5 font-display font-bold text-sm uppercase border-3 border-transparent hover:border-ink hover:bg-cyan transition-all">
					<?= $e($item["label"]) ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<!-- Desktop right actions -->
		<div class="hidden md:flex items-center gap-3">
			<?php if ($isAuth): ?>
				<a href="/profile" class="block shrink-0 transition-transform hover:-translate-x-0.5 hover:-translate-y-0.5" title="Your profile" aria-label="Your profile">
					<?php if ($avatarPath !== null): ?>
						<img src="<?= $e($avatarPath) ?>" alt="<?= $e($username ?? "") ?>" class="w-10 h-10 object-cover border-3 border-ink shadow-brutal-sm hover:shadow-brutal transition-shadow">
					<?php else: ?>
						<span class="w-10 h-10 flex items-center justify-center border-3 border-ink bg-cyan shadow-brutal-sm hover:shadow-brutal transition-shadow">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5" aria-hidden="true">
								<circle cx="12" cy="8" r="4"/>
								<path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>
							</svg>
						</span>
					<?php endif; ?>
				</a>
				<form method="POST" action="/logout" class="inline-block">
					<?= Csrf::field() ?>
					<button type="submit" class="btn-brutal btn-brutal--coral !py-2 !px-3 text-sm">
						Sign out
					</button>
				</form>
			<?php else: ?>
				<a href="/login" class="btn-brutal btn-brutal--cyan !py-2 !px-3 text-sm">
					Sign in
				</a>
				<a href="/register" class="btn-brutal !py-2 !px-3 text-sm">
					Sign up →
				</a>
			<?php endif; ?>
		</div>

		<!-- Mobile burger toggle (pure CSS via <details>) -->
		<details class="md:hidden">
			<summary class="list-none cursor-pointer tile bg-paper border-3 border-ink shadow-brutal-sm hover:bg-lime transition-colors" aria-label="Open menu">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6" aria-hidden="true">
					<line x1="4" y1="7"  x2="20" y2="7"/>
					<line x1="4" y1="12" x2="20" y2="12"/>
					<line x1="4" y1="17" x2="20" y2="17"/>
				</svg>
			</summary>

			<!-- Full-bleed mobile drawer -->
			<div class="fixed inset-x-0 top-[5rem] bg-paper border-b-3 border-ink shadow-brutal-sm px-4 sm:px-6 py-5 flex flex-col gap-2 z-30">
				<?php foreach ($navItems as $item): ?>
					<a href="<?= $e($item["href"]) ?>" class="block px-3 py-2 font-display font-bold text-sm uppercase border-3 border-ink hover:bg-cyan transition-colors">
						<?= $e($item["label"]) ?>
					</a>
				<?php endforeach; ?>

				<hr class="border-t-3 border-ink my-2">

				<?php if ($isAuth): ?>
					<a href="/profile" class="flex items-center gap-3 border-3 border-ink bg-paper px-3 py-2 hover:bg-cyan transition-colors" title="Your profile">
						<?php if ($avatarPath !== null): ?>
							<img src="<?= $e($avatarPath) ?>" alt="" class="w-8 h-8 object-cover border-2 border-ink">
						<?php else: ?>
							<span class="w-8 h-8 flex items-center justify-center border-2 border-ink bg-cyan">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
									<circle cx="12" cy="8" r="4"/>
									<path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>
								</svg>
							</span>
						<?php endif; ?>
						<span class="font-display font-bold text-sm uppercase truncate"><?= $e($username) ?></span>
					</a>
					<form method="POST" action="/logout">
						<?= Csrf::field() ?>
						<button type="submit" class="btn-brutal btn-brutal--coral w-full !py-2 text-sm">
							Sign out
						</button>
					</form>
				<?php else: ?>
					<a href="/login" class="btn-brutal btn-brutal--cyan w-full !py-2 text-sm text-center">
						Sign in
					</a>
					<a href="/register" class="btn-brutal w-full !py-2 text-sm text-center">
						Sign up →
					</a>
				<?php endif; ?>
			</div>
		</details>
	</div>
</header>

<main class="relative z-10 flex-1 w-full">
	<?= $content ?>
</main>


<?php if ($flashSuccess !== null || $flashError !== null): ?>
	<div class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-[calc(100%-3rem)] sm:w-auto pointer-events-none">
		<?php if ($flashSuccess !== null): ?>
			<div class="toast toast--success" data-toast role="status">
				<span class="flex-1"><?= $e($flashSuccess) ?></span>
				<button type="button" class="toast__close" data-toast-close aria-label="Dismiss">×</button>
			</div>
		<?php endif; ?>
		<?php if ($flashError !== null): ?>
			<div class="toast toast--error" data-toast data-toast-duration="6000" role="alert">
				<span class="flex-1"><?= $e($flashError) ?></span>
				<button type="button" class="toast__close" data-toast-close aria-label="Dismiss">×</button>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>

<!-- Footer -->
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
				<?php if (!$isAuth): ?>
					<li><a href="/register" class="hover:text-lime hover:underline decoration-3 underline-offset-4 transition-colors">Sign up</a></li>
				<?php endif; ?>
			</ul>
		</div>

		<!-- Account -->
		<div class="col-span-1 md:col-span-2">
			<p class="font-display font-black text-sm uppercase mb-3">Account</p>
			<ul class="space-y-2 text-sm font-medium text-paper/70">
				<?php if ($isAuth): ?>
					<li><a href="/profile" class="hover:text-lime hover:underline decoration-3 underline-offset-4 transition-colors">Profile</a></li>
					<li>
						<form method="POST" action="/logout" class="inline">
							<?= Csrf::field() ?>
							<button type="submit" class="hover:text-lime hover:underline decoration-3 underline-offset-4 transition-colors">
								Sign out
							</button>
						</form>
					</li>
				<?php else: ?>
					<li><a href="/login" class="hover:text-lime hover:underline decoration-3 underline-offset-4 transition-colors">Sign in</a></li>
					<li><a href="/reset" class="hover:text-lime hover:underline decoration-3 underline-offset-4 transition-colors">Reset password</a></li>
				<?php endif; ?>
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
			<p>42 Angouleme</p>
		</div>
	</div>
</footer>

</body>
</html>
