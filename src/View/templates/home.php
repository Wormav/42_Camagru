<?php
/**
 * Landing page — neo-brutalist hero, snap mosaic, features, stats, CTA.
 *
 * @var \Closure(mixed): string $e            Escape helper.
 * @var string                  $title        Page title.
 * @var int                     $totalSnaps   Total snaps in the DB.
 * @var int                     $totalCreators Total distinct creators.
 * @var bool                    $isAuth       Whether the visitor is signed in.
 */

?>

<!-- ============================================================ -->
<!-- HERO                                                          -->
<!-- ============================================================ -->
<section class="relative overflow-hidden bg-paper">
	<!-- Decorative offset blocks behind the hero. Pure decoration, aria-hidden. -->
	<div aria-hidden="true" class="absolute -top-10 -left-10 w-40 h-40 bg-lime border-3 border-ink rotate-[-12deg] hidden md:block"></div>
	<div aria-hidden="true" class="absolute top-20 right-10 w-24 h-24 bg-pink border-3 border-ink rotate-[8deg] hidden md:block"></div>
	<div aria-hidden="true" class="absolute bottom-10 left-1/3 w-16 h-16 bg-cyan border-3 border-ink rotate-[20deg] hidden md:block"></div>

	<div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-20 sm:py-28 grid lg:grid-cols-12 gap-10 items-center">

		<!-- Left: copy + CTA -->
		<div class="lg:col-span-7 flex flex-col gap-6 animate-rise">
			<span class="brutal-tag bg-cyan self-start">// 42 project</span>

			<h1 class="font-display font-black text-5xl sm:text-6xl lg:text-7xl leading-[0.95] tracking-tight uppercase">
				Snap it.
				<span class="highlight highlight--pink inline-block">Stack it.</span>
				<br>
				<span class="highlight inline-block">Ship it.</span>
			</h1>

			<p class="font-sans text-lg sm:text-xl max-w-xl">
				A photo booth that takes your webcam, slaps a sticker on top, and hands you a snap good enough to print on a t-shirt. Built from scratch in PHP, like in the good old days.
			</p>

			<div class="flex flex-wrap items-center gap-3 mt-2">
				<?php if ($isAuth): ?>
					<a href="/post" class="btn-brutal">
						Start snapping →
					</a>
					<a href="/gallery" class="btn-brutal btn-brutal--white">
						Browse the gallery
					</a>
				<?php else: ?>
					<a href="/register" class="btn-brutal">
						Get started →
					</a>
					<a href="/gallery" class="btn-brutal btn-brutal--white">
						Peek the gallery
					</a>
				<?php endif; ?>
			</div>

			<!-- Stats strip -->
			<dl class="flex flex-wrap items-center gap-6 mt-6">
				<div>
					<dt class="font-mono text-xs uppercase tracking-wider opacity-60">Snaps captured</dt>
					<dd class="font-display font-black text-3xl"><?= $e($totalSnaps) ?></dd>
				</div>
				<div>
					<dt class="font-mono text-xs uppercase tracking-wider opacity-60">Creators</dt>
					<dd class="font-display font-black text-3xl"><?= $e($totalCreators) ?></dd>
				</div>
				<div>
					<dt class="font-mono text-xs uppercase tracking-wider opacity-60">Overlays</dt>
					<dd class="font-display font-black text-3xl">5+</dd>
				</div>
			</dl>
		</div>

		<!-- Right: two photos stacked with offset rotation, polaroid-brutal vibe -->
		<div class="lg:col-span-5 relative">
			<div class="relative w-full max-w-md mx-auto" style="aspect-ratio: 4/5;">

				<!-- Back photo: lower-left, slight counter-rotation. -->
				<figure class="absolute bottom-0 left-0 border-3 border-ink bg-paper p-2 sm:p-3 shadow-brutal z-10" style="width: 68%; aspect-ratio: 4/5; transform: rotate(-4deg);">
					<img
						src="/assets/photos/hero-2.jpg"
						alt=""
						class="w-full h-full object-cover border-2 border-ink"
					>
					<figcaption class="absolute -bottom-2 left-1/2 -translate-x-1/2 brutal-tag bg-cyan whitespace-nowrap text-[0.65rem]">
						// snap_001
					</figcaption>
				</figure>

				<!-- Front photo: upper-right, slight positive rotation, bigger shadow. -->
				<figure class="absolute top-0 right-0 border-3 border-ink bg-paper p-2 sm:p-3 shadow-brutal-lg z-20" style="width: 68%; aspect-ratio: 4/5; transform: rotate(3deg);">
					<img
						src="/assets/photos/hero-1.jpg"
						alt=""
						class="w-full h-full object-cover border-2 border-ink"
					>
					<!-- Sticker pin -->
					<span aria-hidden="true" class="sticker absolute -top-3 -right-3">LIVE</span>
					<figcaption class="absolute -bottom-2 left-1/2 -translate-x-1/2 brutal-tag bg-lime whitespace-nowrap text-[0.65rem]">
						// snap_002
					</figcaption>
				</figure>

			</div>
		</div>

	</div>
</section>
