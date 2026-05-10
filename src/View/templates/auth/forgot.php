<?php
/**
 * Forgot-password form (GET /reset).
 *
 * @var \Closure(mixed): string $e       Escape helper.
 * @var string                  $title   Page title.
 * @var array<int, string>      $errors  Server-side validation errors.
 * @var array<string, string>   $old     Previously submitted values (email).
 */

use App\Core\Csrf;

?>
<section class="max-w-xl mx-auto px-4 sm:px-6 py-16 sm:py-20">

	<header class="mb-8">
		<span class="brutal-tag bg-pink">→ Forgot password</span>
		<h1 class="font-display font-black text-display-lg mt-4 leading-[0.95]">
			Reset your <span class="highlight highlight--pink">password</span>
		</h1>
		<p class="mt-4 text-sm font-mono">
			// Remembered it?
			<a href="/login" class="underline decoration-3 underline-offset-4 hover:bg-lime px-1 transition-colors">
				Sign in
			</a>
		</p>
	</header>

	<div class="brutal-card bg-paper">

		<?php if (!empty($errors)): ?>
			<div class="brutal-error mb-6" role="alert">
				<p>Please review your information.</p>
				<ul>
					<?php foreach ($errors as $error): ?>
						<li><?= $e($error) ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<p class="text-sm font-medium mb-5 leading-relaxed">
			Enter the email tied to your account. If it matches a verified user,
			we'll send a reset link valid for one hour.
		</p>

		<form id="forgot-form" method="POST" action="/reset" novalidate class="space-y-5">
			<?= Csrf::field() ?>

			<div>
				<label for="email" class="brutal-label">Email</label>
				<input
					id="email"
					name="email"
					type="email"
					required
					autocomplete="email"
					value="<?= $e($old["email"] ?? "") ?>"
					class="brutal-input"
					placeholder="you@example.com"
				>
			</div>

			<button type="submit" class="btn-brutal w-full mt-2">
				Send reset link →
			</button>
		</form>
	</div>

	<p class="mt-6 text-xs font-mono text-center opacity-60">
		// We never reveal whether an email is registered.
	</p>
</section>
