<?php
/**
 * Verification result page (GET /verify?token=...).
 *
 * @var \Closure(mixed): string $e        Escape helper.
 * @var string                  $title    Page title.
 * @var bool                    $success  Whether the token was accepted.
 */
?>
<section class="max-w-xl mx-auto px-4 sm:px-6 py-16 sm:py-24 text-center">

	<?php if ($success): ?>

		<span class="brutal-tag bg-lime mx-auto">★ Verified</span>

		<h1 class="font-display font-black text-display-lg mt-5 leading-[0.95]">
			You're <span class="highlight">in</span>
		</h1>

		<p class="mt-6 text-base font-medium max-w-md mx-auto leading-relaxed">
			Your account is now active. Sign in and post your first capture.
		</p>

		<div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-3">
			<a href="/login" class="btn-brutal">
				Sign in →
			</a>
			<a href="/" class="btn-brutal btn-brutal--white">
				Back home
			</a>
		</div>

	<?php else: ?>

		<span class="brutal-tag bg-coral mx-auto">★ Verification failed</span>

		<h1 class="font-display font-black text-display-lg mt-5 leading-[0.95]">
			This link is <span class="highlight highlight--pink">invalid</span>
		</h1>

		<p class="mt-6 text-base font-medium max-w-md mx-auto leading-relaxed">
			The token is unknown, expired, or has already been used.
			If you've already verified, just sign in.
		</p>

		<div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-3">
			<a href="/login" class="btn-brutal">
				Sign in →
			</a>
			<a href="/register" class="btn-brutal btn-brutal--white">
				Sign up again
			</a>
		</div>

	<?php endif; ?>

</section>
