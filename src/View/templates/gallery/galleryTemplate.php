<?php
$pageUrl = static function (int $n): string {
	return "/gallery?page=" . $n;
};

$paginationWindow = static function (int $page, int $totalPages): array {
	if ($totalPages <= 7) {
		return range(1, $totalPages);
	}
	$pages = [1];
	$start = max(2, $page - 1);
	$end   = min($totalPages - 1, $page + 1);
	if ($start > 2) {
		$pages[] = "...";
	}
	for ($i = $start; $i <= $end; $i++) {
		$pages[] = $i;
	}
	if ($end < $totalPages - 1) {
		$pages[] = "...";
	}
	$pages[] = $totalPages;
	return $pages;
};
$pages = $paginationWindow($page, $totalPages);
?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-10 sm:py-14">

	<header class="mb-8 sm:mb-12 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
		<div>
			<span class="brutal-tag bg-cyan">★ Public feed</span>
			<h1 class="font-display font-black text-display-lg mt-4 leading-[0.95]">
				The <span class="highlight highlight--pink">gallery</span>
			</h1>
			<p class="mt-4 font-mono text-xs uppercase tracking-widest opacity-70">
				// <?= $e($total) ?> snap<?= $total === 1 ? "" : "s" ?> total · page <?= $e($page) ?> of <?= $e($totalPages) ?>
			</p>
		</div>

		<?php if ($total > 0 && $totalPages > 1): ?>
			<div data-mode-toggle class="inline-flex border-3 border-ink shadow-brutal-sm bg-paper self-start sm:self-end">
				<button
					type="button"
					data-mode-option="pagination"
					class="font-display font-black text-xs uppercase tracking-wider px-3 py-2 transition-colors data-[active=true]:bg-lime hover:bg-lime/60"
					data-active="true"
					aria-pressed="true"
				>
					Pagination
				</button>
				<button
					type="button"
					data-mode-option="infinite"
					class="font-display font-black text-xs uppercase tracking-wider px-3 py-2 border-l-3 border-ink transition-colors data-[active=true]:bg-lime hover:bg-lime/60"
					data-active="false"
					aria-pressed="false"
				>
					Infinite scroll
				</button>
			</div>
		<?php endif; ?>
	</header>

	<?php if ($total === 0): ?>

		<div class="brutal-card bg-paper !p-10 sm:!p-14 text-center">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-14 h-14 mx-auto opacity-60" aria-hidden="true">
				<rect x="3" y="3" width="18" height="18" rx="0"/>
				<circle cx="9" cy="9" r="2"/>
				<polyline points="21 15 16 10 5 21"/>
			</svg>
			<p class="mt-4 font-display font-black text-xl uppercase">Nothing here yet</p>
			<p class="mt-2 font-mono text-xs opacity-60">
				// be the first to share a snap.
			</p>
			<a href="/post" class="btn-brutal btn-brutal--cyan mt-6 inline-flex">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
					<rect x="3" y="6" width="18" height="13" rx="0"/>
					<circle cx="12" cy="12.5" r="3.5"/>
					<path d="M8 6l1.5-2h5L16 6"/>
				</svg>
				Make a snap
			</a>
		</div>

	<?php else: ?>

		<ul
			data-gallery-grid
			data-current-page="<?= $e($page) ?>"
			data-total-pages="<?= $e($totalPages) ?>"
			class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6"
		>
			<?php foreach ($items as $item): ?>
				<?php include __DIR__ . "/_cardTemplate.php"; ?>
			<?php endforeach; ?>
		</ul>

		<div data-infinite-status class="hidden mt-10 font-mono text-xs uppercase tracking-widest opacity-70">
			<svg data-infinite-loader viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="hidden w-6 h-6 animate-spin" aria-hidden="true">
				<path d="M21 12a9 9 0 1 1-6.219-8.56"/>
			</svg>
			<p data-infinite-message>// loading more snaps…</p>
		</div>

		<div data-infinite-sentinel class="hidden h-px w-full" aria-hidden="true"></div>

		<?php if ($totalPages > 1): ?>
			<nav data-pagination-nav class="mt-10 sm:mt-14 flex items-center justify-center gap-2 flex-wrap" aria-label="Gallery pagination">

				<?php if ($page > 1): ?>
					<a href="<?= $e($pageUrl($page - 1)) ?>" class="btn-brutal btn-brutal--white !py-2 !px-3 text-sm" rel="prev">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
							<polyline points="15 18 9 12 15 6"/>
						</svg>
						Prev
					</a>
				<?php else: ?>
					<span class="btn-brutal btn-brutal--white !py-2 !px-3 text-sm" aria-disabled="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
							<polyline points="15 18 9 12 15 6"/>
						</svg>
						Prev
					</span>
				<?php endif; ?>

				<?php foreach ($pages as $p): ?>
					<?php if ($p === "..."): ?>
						<span class="px-2 font-mono text-sm opacity-60" aria-hidden="true">…</span>
					<?php elseif ($p === $page): ?>
						<span class="btn-brutal !py-2 !px-3 text-sm" aria-current="page">
							<?= $e((int) $p) ?>
						</span>
					<?php else: ?>
						<a href="<?= $e($pageUrl((int) $p)) ?>" class="btn-brutal btn-brutal--white !py-2 !px-3 text-sm">
							<?= $e((int) $p) ?>
						</a>
					<?php endif; ?>
				<?php endforeach; ?>

				<?php if ($page < $totalPages): ?>
					<a href="<?= $e($pageUrl($page + 1)) ?>" class="btn-brutal btn-brutal--white !py-2 !px-3 text-sm" rel="next">
						Next
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
							<polyline points="9 18 15 12 9 6"/>
						</svg>
					</a>
				<?php else: ?>
					<span class="btn-brutal btn-brutal--white !py-2 !px-3 text-sm" aria-disabled="true">
						Next
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
							<polyline points="9 18 15 12 9 6"/>
						</svg>
					</span>
				<?php endif; ?>

			</nav>
		<?php endif; ?>

	<?php endif; ?>
</section>
