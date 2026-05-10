<?php
/**
 * New-password form (GET /reset/confirm?token=...).
 *
 * @var \Closure(mixed): string $e       Escape helper.
 * @var string                  $title   Page title.
 * @var string                  $token   Reset token (passed through the form).
 * @var array<int, string>      $errors  Server-side validation errors.
 * @var array<string, string>   $old     Previously submitted values (unused — we never echo passwords).
 */

use App\Core\Csrf;

?>
<section class="max-w-xl mx-auto px-4 sm:px-6 py-16 sm:py-20">

	<header class="mb-8">
		<span class="brutal-tag bg-lime">★ New password</span>
		<h1 class="font-display font-black text-display-lg mt-4 leading-[0.95]">
			Pick a <span class="highlight">new password</span>
		</h1>
		<p class="mt-4 text-sm font-mono">
			// One last step before signing in again.
		</p>
	</header>

	<div class="brutal-card bg-paper">

		<?php if (!empty($errors)): ?>
			<div class="brutal-error mb-6" role="alert">
				<p>Please review your password.</p>
				<ul>
					<?php foreach ($errors as $error): ?>
						<li><?= $e($error) ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<form id="reset-form" method="POST" action="/reset/confirm" novalidate class="space-y-5">
			<?= Csrf::field() ?>
			<input type="hidden" name="token" value="<?= $e($token) ?>">

			<div>
				<label for="password" class="brutal-label">New password</label>
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
				Update password →
			</button>
		</form>
	</div>
</section>
