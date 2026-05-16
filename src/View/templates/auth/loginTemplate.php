<?php
/**
 * Sign-in form (GET /login).
 *
 * @var \Closure(mixed): string $e       Escape helper.
 * @var string                  $title   Page title.
 * @var array<int, string>      $errors  Server-side validation errors.
 * @var array<string, string>   $old     Previously submitted values (username only — never the password).
 */

use App\Core\CsrfCore;

?>
<section class="max-w-xl mx-auto px-4 sm:px-6 py-16 sm:py-20">

	<header class="mb-8">
		<span class="brutal-tag bg-cyan">→ Welcome back</span>
		<h1 class="font-display font-black text-display-lg mt-4 leading-[0.95]">
			Sign in to <span class="highlight">Camagru</span>
		</h1>
		<p class="mt-4 text-sm font-mono">
			// New here?
			<a href="/register" class="underline decoration-3 underline-offset-4 hover:bg-lime px-1 transition-colors">
				Create an account
			</a>
		</p>
	</header>

	<div class="brutal-card bg-paper">

		<?php if (!empty($errors)): ?>
			<div class="brutal-error mb-6" role="alert">
				<p>Invalid credentials.</p>
			</div>
		<?php endif; ?>

		<form id="login-form" method="POST" action="/login" novalidate class="space-y-5">
			<?= CsrfCore::field() ?>

			<div>
				<label for="username" class="brutal-label">Username</label>
				<input
					id="username"
					name="username"
					type="text"
					required
					autocomplete="username"
					value="<?= $e($old["username"] ?? "") ?>"
					class="brutal-input"
					placeholder="jdoe"
				>
			</div>

			<div>
				<label for="password" class="brutal-label">Password</label>
				<input
					id="password"
					name="password"
					type="password"
					required
					autocomplete="current-password"
					class="brutal-input"
					placeholder="••••••••"
				>
			</div>

			<button type="submit" class="btn-brutal w-full mt-2">
				Sign in →
			</button>
		</form>

		<p class="mt-6 text-xs font-mono text-center opacity-60">
			// <a href="/reset" class="underline decoration-2 underline-offset-4 hover:bg-pink px-1 transition-colors">Forgot password?</a>
		</p>
	</div>
</section>
