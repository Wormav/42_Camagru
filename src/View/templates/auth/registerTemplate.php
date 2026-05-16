<?php
/**
 * Sign-up form (GET /register).
 *
 * @var \Closure(mixed): string $e       Escape helper.
 * @var string                  $title   Page title.
 * @var array<int, string>      $errors  Server-side validation errors.
 * @var array<string, string>   $old     Previously submitted values.
 */

use App\Core\CsrfCore;

?>
<section class="max-w-xl mx-auto px-4 sm:px-6 py-16 sm:py-20">

	<header class="mb-8">
		<span class="brutal-tag bg-lime">★ New here</span>
		<h1 class="font-display font-black text-display-lg mt-4 leading-[0.95]">
			Create your <span class="highlight highlight--pink">account</span>
		</h1>
		<p class="mt-4 text-sm font-mono">
			// Already have one?
			<a href="/login" class="underline decoration-3 underline-offset-4 hover:bg-lime px-1 transition-colors">
				Sign in
			</a>
		</p>
	</header>

	<div class="brutal-card bg-paper">

		<?php if (!empty($errors)): ?>
			<div class="brutal-error mb-6" role="alert">
				<p>Sign-up failed. Please review your information.</p>
				<?php if (count($errors) > 0): ?>
					<ul>
						<?php foreach ($errors as $error): ?>
							<li><?= $e($error) ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<form id="register-form" method="POST" action="/register" novalidate class="space-y-5">
			<?= CsrfCore::field() ?>

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

			<div>
				<label for="username" class="brutal-label">Username</label>
				<input
					id="username"
					name="username"
					type="text"
					required
					minlength="3"
					maxlength="20"
					pattern="[A-Za-z0-9_]+"
					autocomplete="username"
					value="<?= $e($old["username"] ?? "") ?>"
					class="brutal-input"
					placeholder="jdoe"
				>
				<span class="brutal-help">3 to 20 chars · letters, digits, underscore</span>
			</div>

			<div>
				<label for="password" class="brutal-label">Password</label>
				<input
					id="password"
					name="password"
					type="password"
					required
					minlength="8"
					autocomplete="new-password"
					class="brutal-input"
					placeholder="••••••••"
				>
				<span class="brutal-help">8+ chars · 1 uppercase · 1 lowercase · 1 digit · 1 special</span>
			</div>

			<div>
				<label for="password_confirmation" class="brutal-label">Confirm password</label>
				<input
					id="password_confirmation"
					name="password_confirmation"
					type="password"
					required
					autocomplete="new-password"
					class="brutal-input"
					placeholder="••••••••"
				>
			</div>

			<button type="submit" class="btn-brutal w-full mt-2">
				Create account →
			</button>
		</form>
	</div>

	<p class="mt-6 text-xs font-mono text-center opacity-60">
		// By signing up you agree to receive a verification email.
	</p>
</section>

<script src="/dist/register.bundle.js" defer></script>
