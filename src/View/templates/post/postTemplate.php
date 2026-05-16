<?php
/**
 * @var \Closure(mixed): string $e         Escape helper.
 * @var string                  $title     Page title.
 * @var array<string,array{id:string,label:string,path:string}> $overlays
 * @var array<int,array{id:int,user_id:int,image_path:string,overlay_used:?string,created_at:string}> $userImages
 */
$imageCount = count($userImages);
?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-10 sm:py-14">

	<header class="mb-8 sm:mb-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
		<div>
			<span class="brutal-tag bg-lime">★ Studio</span>
			<h1 class="font-display font-black text-display-lg mt-4 leading-[0.95]">
				Make a <span class="highlight highlight--pink">snap</span>
			</h1>
			<p class="mt-4 font-mono text-xs uppercase tracking-widest opacity-70">
				// pick an overlay, frame yourself, capture.
			</p>
		</div>
	</header>

	<div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6 lg:gap-8">

		<div class="space-y-6">

			<section class="brutal-card bg-paper !p-5 sm:!p-6">
				<div class="flex items-center justify-between gap-3 mb-4">
					<p class="brutal-label !mb-0">Stage</p>
					<span class="brutal-tag bg-cyan">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5" aria-hidden="true">
							<rect x="3" y="6" width="18" height="13" rx="0"/>
							<circle cx="12" cy="12.5" r="3.5"/>
							<path d="M8 6l1.5-2h5L16 6"/>
						</svg>
						LIVE
					</span>
				</div>

				<div class="relative w-full aspect-video border-3 border-ink bg-ink overflow-hidden">

					<video
						data-stage-video
						autoplay
						muted
						playsinline
						class="hidden"
					></video>

					<canvas
						data-stage-canvas
						class="absolute inset-0 w-full h-full object-cover hidden"
					></canvas>

					<img
						data-stage-image
						alt=""
						class="absolute inset-0 w-full h-full object-contain bg-ink hidden"
					>

					<div data-stage-status data-stage-state="idle" class="absolute inset-0 flex flex-col items-center justify-center gap-3 text-paper">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-14 h-14 opacity-70" aria-hidden="true">
							<rect x="3" y="6" width="18" height="13" rx="0"/>
							<circle cx="12" cy="12.5" r="3.5"/>
							<path d="M8 6l1.5-2h5L16 6"/>
						</svg>
						<p data-stage-status-label class="font-mono text-xs uppercase tracking-widest opacity-70">
							// webcam preview goes here
						</p>
					</div>
				</div>

				<div class="mt-5 flex flex-col sm:flex-row gap-3">
					<button type="button" data-action="use-webcam" class="btn-brutal btn-brutal--cyan flex-1">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
							<rect x="3" y="6" width="18" height="13" rx="0"/>
							<circle cx="12" cy="12.5" r="3.5"/>
						</svg>
						Use webcam
					</button>

					<label class="btn-brutal btn-brutal--white flex-1 cursor-pointer">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
							<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
							<polyline points="17 8 12 3 7 8"/>
							<line x1="12" y1="3" x2="12" y2="15"/>
						</svg>
						Upload image
						<input type="file" data-action="use-upload" accept="image/jpeg,image/png" class="hidden">
					</label>
				</div>
			</section>

			<section class="brutal-card bg-paper !p-5 sm:!p-6">
				<div class="flex items-center justify-between gap-3 mb-4">
					<p class="brutal-label !mb-0">Overlays</p>
					<span class="font-mono text-xs uppercase tracking-widest opacity-60">// pick one</span>
				</div>

				<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3" data-overlay-list>
					<?php foreach ($overlays as $overlay): ?>
						<button
							type="button"
							data-overlay-id="<?= $e($overlay["id"]) ?>"
							data-overlay-path="<?= $e($overlay["path"]) ?>"
							aria-pressed="false"
							class="overlay-tile group"
							title="<?= $e($overlay["label"]) ?>"
						>
							<span class="overlay-tile__thumb">
								<img
									src="<?= $e($overlay["path"]) ?>"
									alt=""
									loading="lazy"
									class="w-full h-full object-contain"
								>
							</span>
							<span class="overlay-tile__label">
								<?= $e($overlay["label"]) ?>
							</span>
						</button>
					<?php endforeach; ?>
				</div>
			</section>

			<div class="flex justify-end">
				<button
					type="button"
					data-action="capture"
					disabled
					aria-disabled="true"
					class="btn-brutal btn-brutal--ink w-full sm:w-auto"
				>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
						<circle cx="12" cy="12" r="9"/>
						<circle cx="12" cy="12" r="3"/>
					</svg>
					Capture snap
				</button>
			</div>
		</div>

		<aside class="space-y-6 lg:sticky lg:top-6 lg:self-start">

			<section class="brutal-card bg-paper !p-5 sm:!p-6">
				<div class="flex items-center justify-between gap-3 mb-4">
					<p class="brutal-label !mb-0">Your snaps</p>
					<span class="brutal-tag bg-pink" data-snaps-count><?= $e($imageCount) ?></span>
				</div>

				<div
					data-snaps-empty
					class="border-3 border-ink bg-white p-6 text-center<?= $imageCount > 0 ? " hidden" : "" ?>"
				>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-10 h-10 mx-auto opacity-60" aria-hidden="true">
						<rect x="3" y="3" width="18" height="18" rx="0"/>
						<circle cx="9" cy="9" r="2"/>
						<polyline points="21 15 16 10 5 21"/>
					</svg>
					<p class="mt-3 font-display font-black text-sm uppercase">No snaps yet</p>
					<p class="mt-2 font-mono text-xs opacity-60">
						// your captures will appear here.
					</p>
				</div>

				<div
					data-snaps-list
					class="grid grid-cols-2 gap-3<?= $imageCount === 0 ? " hidden" : "" ?>"
				>
					<?php foreach ($userImages as $image): ?>
						<figure
							data-snap-item
							data-image-id="<?= $e($image["id"]) ?>"
							class="snap-tile group"
						>
							<button
								type="button"
								data-action="open-lightbox"
								data-image-src="<?= $e($image["image_path"]) ?>"
								class="snap-tile__thumb"
								aria-label="View snap"
							>
								<img
									src="<?= $e($image["image_path"]) ?>"
									alt="Snap"
									loading="lazy"
									class="w-full h-full object-cover"
								>
							</button>
							<button
								type="button"
								data-action="delete-snap"
								class="snap-tile__delete"
								aria-label="Delete snap"
								title="Delete"
							>
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
									<polyline points="3 6 5 6 21 6"/>
									<path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
									<path d="M10 11v6"/>
									<path d="M14 11v6"/>
									<path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
								</svg>
							</button>
						</figure>
					<?php endforeach; ?>
				</div>
			</section>

			<section class="border-3 border-ink bg-lime shadow-brutal p-5">
				<p class="font-display font-black text-xs uppercase tracking-wide mb-2">// tip</p>
				<p class="font-mono text-xs leading-relaxed">
					Allow camera access when your browser asks, or upload a picture instead.
					Then select an overlay to unlock the capture button.
				</p>
			</section>
		</aside>
	</div>
</section>

<div
	data-lightbox
	data-open="false"
	class="fixed inset-0 z-50 hidden items-center justify-center bg-ink/85 p-4 sm:p-8"
	role="dialog"
	aria-modal="true"
	aria-label="Snap preview"
>
	<button
		type="button"
		data-action="close-lightbox"
		aria-label="Close"
		class="absolute top-4 right-4 sm:top-6 sm:right-6 inline-flex items-center justify-center w-12 h-12 border-3 border-ink bg-paper shadow-brutal hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-brutal-lg transition-transform"
	>
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5" aria-hidden="true">
			<line x1="18" y1="6" x2="6" y2="18"/>
			<line x1="6" y1="6" x2="18" y2="18"/>
		</svg>
	</button>

	<figure class="max-w-5xl max-h-full">
		<img
			data-lightbox-image
			src=""
			alt="Snap preview"
			class="block max-w-full max-h-[85vh] border-3 border-ink shadow-brutal-lg bg-ink"
		>
	</figure>
</div>

<template data-snap-template>
	<figure data-snap-item data-image-id="" class="snap-tile group">
		<button type="button" data-action="open-lightbox" data-image-src="" class="snap-tile__thumb" aria-label="View snap">
			<img src="" alt="Snap" loading="lazy" class="w-full h-full object-cover">
		</button>
		<button type="button" data-action="delete-snap" class="snap-tile__delete" aria-label="Delete snap" title="Delete">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
				<polyline points="3 6 5 6 21 6"/>
				<path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
				<path d="M10 11v6"/>
				<path d="M14 11v6"/>
				<path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
			</svg>
		</button>
	</figure>
</template>
