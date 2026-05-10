<?php
/**
 * Post-signup confirmation page (GET /register/check-email).
 *
 * @var \Closure(mixed): string $e      Escape helper.
 * @var string                  $title  Page title.
 */
?>
<section class="max-w-xl mx-auto px-4 sm:px-6 py-16 sm:py-24 text-center">

	<span class="brutal-tag bg-cyan mx-auto">★ Almost there</span>

	<h1 class="font-display font-black text-display-lg mt-5 leading-[0.95]">
		Check your <span class="highlight highlight--cyan">inbox</span>
	</h1>

	<p class="mt-6 text-base font-medium max-w-md mx-auto leading-relaxed">
		We sent a verification link to your email. Click it to activate your
		account — without it, you can't sign in yet.
	</p>

	<div class="mt-10 brutal-card bg-paper text-left max-w-md mx-auto">
		<p class="font-mono text-xs uppercase tracking-widest mb-3 opacity-60">// What's next</p>
		<ol class="space-y-2 text-sm font-medium list-decimal pl-5">
			<li>Open the email we just sent.</li>
			<li>Click the verification link.</li>
			<li>Sign in and start posting.</li>
		</ol>
	</div>

	<p class="mt-8 text-xs font-mono opacity-60">
		// Didn't receive anything? Check your spam folder.
	</p>

	<a href="/" class="btn-brutal btn-brutal--white mt-8 inline-flex">
		← Back home
	</a>
</section>
