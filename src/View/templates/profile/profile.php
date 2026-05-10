<?php
/**
 * Profile page (GET /profile) — read-only view of the current user.
 * Edit forms (username, email, password, notifications, avatar) come next.
 *
 * @var \Closure(mixed): string $e            Escape helper.
 * @var string                  $title        Page title.
 * @var array<string, mixed>    $currentUser  Row from the users table.
 */

$memberSince = date("F j, Y", strtotime($currentUser["created_at"]));
$hasAvatar   = $currentUser["avatar_path"] !== null && $currentUser["avatar_path"] !== "";
$notifyOn    = (int) $currentUser["notify_comments"] === 1;
?>
<section class="max-w-xl mx-auto px-4 sm:px-6 py-16 sm:py-20">

	<header class="mb-8">
		<span class="brutal-tag bg-pink">★ Your profile</span>
		<h1 class="font-display font-black text-display-lg mt-4 leading-[0.95]">
			Hey, <span class="highlight"><?= $e($currentUser["username"]) ?></span>
		</h1>
		<p class="mt-4 text-sm font-mono">
			// Manage your account and your face.
		</p>
	</header>

	<!-- Avatar -->
	<div class="brutal-card bg-paper text-center mb-6">
		<p class="font-mono text-xs uppercase tracking-widest opacity-60 mb-5">// Avatar</p>

		<?php if ($hasAvatar): ?>
			<img
				src="<?= $e($currentUser["avatar_path"]) ?>"
				alt="Your avatar"
				class="w-32 h-32 object-cover border-3 border-ink shadow-brutal mx-auto"
			>
			<p class="mt-4 font-mono text-xs opacity-70">// uploaded</p>
		<?php else: ?>
			<div class="w-32 h-32 flex items-center justify-center border-3 border-ink bg-cyan shadow-brutal mx-auto">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-14 h-14" aria-hidden="true">
					<circle cx="12" cy="8" r="4"/>
					<path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>
				</svg>
			</div>
			<p class="mt-4 font-mono text-xs opacity-70">// no avatar yet</p>
		<?php endif; ?>
	</div>

	<!-- Identity -->
	<div class="brutal-card bg-paper mb-6">
		<p class="font-mono text-xs uppercase tracking-widest opacity-60 mb-5">// Identity</p>

		<div class="space-y-5">
			<div>
				<p class="brutal-label">Username</p>
				<p class="font-mono text-sm break-all"><?= $e($currentUser["username"]) ?></p>
			</div>

			<div>
				<p class="brutal-label">Email</p>
				<p class="font-mono text-sm break-all"><?= $e($currentUser["email"]) ?></p>
			</div>

			<div>
				<p class="brutal-label">Password</p>
				<p class="font-mono text-sm">••••••••</p>
			</div>
		</div>
	</div>

	<!-- Preferences -->
	<div class="brutal-card bg-paper mb-6">
		<p class="font-mono text-xs uppercase tracking-widest opacity-60 mb-5">// Preferences</p>

		<p class="brutal-label">Comment notifications</p>
		<?php if ($notifyOn): ?>
			<span class="inline-block px-2 py-0.5 bg-lime border-2 border-ink font-display font-black text-xs">ON</span>
			<p class="mt-2 font-mono text-xs opacity-70">// emails sent when someone comments on your photos.</p>
		<?php else: ?>
			<span class="inline-block px-2 py-0.5 bg-coral border-2 border-ink font-display font-black text-xs">OFF</span>
			<p class="mt-2 font-mono text-xs opacity-70">// no email when comments are posted.</p>
		<?php endif; ?>
	</div>

	<!-- Meta -->
	<div class="brutal-card bg-paper">
		<p class="font-mono text-xs uppercase tracking-widest opacity-60 mb-5">// Meta</p>

		<p class="brutal-label">Member since</p>
		<p class="font-mono text-sm"><?= $e($memberSince) ?></p>
	</div>

	<p class="mt-8 text-xs font-mono opacity-60 text-center">
		// Edit forms coming up next — we're building this page incrementally.
	</p>

</section>
