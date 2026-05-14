<?php
/**
 * Public gallery — paginated grid of all user snaps.
 *
 * @var \Closure(mixed): string $e          Escape helper.
 * @var string                  $title      Page title.
 * @var array<int,array{
 *     id:int,
 *     user_id:int,
 *     image_path:string,
 *     overlay_used:?string,
 *     created_at:string,
 *     username:string,
 *     like_count:int,
 *     comment_count:int,
 *     user_has_liked:int
 * }> $items
 * @var int       $total          Total images in DB.
 * @var int       $page           Current page (1-based, already clamped).
 * @var int       $totalPages     Total pages (at least 1).
 * @var bool      $isAuth         True if a user is currently logged in.
 * @var int|null  $currentUserId  Logged-in user id (null for visitors).
 */

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

		<ul data-gallery-grid class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
			<?php foreach ($items as $item): ?>
				<?php
				$createdTs    = strtotime((string) $item["created_at"]);
				$createdHuman = $createdTs !== false ? date("M j, Y", $createdTs) : "";
				$createdIso   = $createdTs !== false ? date("c", $createdTs) : "";
				?>
				<li>
					<figure class="brutal-card bg-paper !p-0 overflow-hidden flex flex-col h-full">
						<div class="aspect-square w-full bg-ink border-b-3 border-ink overflow-hidden">
							<img
								src="<?= $e($item["image_path"]) ?>"
								alt="Snap by <?= $e($item["username"]) ?>"
								loading="lazy"
								class="w-full h-full object-cover"
							>
						</div>

						<figcaption class="p-4 sm:p-5 flex flex-col gap-3 flex-1">
							<div class="flex items-center justify-between gap-3">
								<p class="font-display font-black text-sm uppercase truncate">
									@<?= $e($item["username"]) ?>
								</p>
								<?php if ($createdIso !== ""): ?>
									<time datetime="<?= $e($createdIso) ?>" class="font-mono text-[0.7rem] uppercase tracking-wider opacity-60 shrink-0">
										<?= $e($createdHuman) ?>
									</time>
								<?php endif; ?>
							</div>

							<div class="flex items-center gap-2 mt-auto">
								<?php
								$liked = (int) ($item["user_has_liked"] ?? 0) === 1;
								?>
								<?php if ($isAuth): ?>
									<button
										type="button"
										data-action="toggle-like"
										data-image-id="<?= $e((int) $item["id"]) ?>"
										data-liked="<?= $liked ? "true" : "false" ?>"
										aria-pressed="<?= $liked ? "true" : "false" ?>"
										class="brutal-tag inline-flex items-center gap-1.5 transition-colors <?= $liked ? "bg-pink" : "bg-paper hover:bg-pink" ?>"
										aria-label="<?= $liked ? "Unlike" : "Like" ?>"
									>
										<svg viewBox="0 0 24 24" fill="<?= $liked ? "currentColor" : "none" ?>" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5" aria-hidden="true" data-like-icon>
											<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
										</svg>
										<span data-like-count><?= $e((int) $item["like_count"]) ?></span>
									</button>
								<?php else: ?>
									<a
										href="/login"
										class="brutal-tag bg-paper inline-flex items-center gap-1.5"
										title="Sign in to like"
									>
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5" aria-hidden="true">
											<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
										</svg>
										<?= $e((int) $item["like_count"]) ?>
									</a>
								<?php endif; ?>

								<span class="brutal-tag bg-cyan inline-flex items-center gap-1.5">
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5" aria-hidden="true">
										<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
									</svg>
									<?= $e((int) $item["comment_count"]) ?>
								</span>
							</div>
						</figcaption>
					</figure>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ($totalPages > 1): ?>
			<nav class="mt-10 sm:mt-14 flex items-center justify-center gap-2 flex-wrap" aria-label="Gallery pagination">

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
