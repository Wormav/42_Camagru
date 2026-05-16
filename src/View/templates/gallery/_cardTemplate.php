<?php
/**
 * One gallery card partial — used by gallery.php (server render) and the
 * infinite scroll JSON endpoint.
 *
 * Expected scope:
 *  - $e        : escape helper
 *  - $item     : array shape matching ImageModel::findFeed row
 *  - $isAuth   : bool
 */

$createdTs    = strtotime((string) $item["created_at"]);
$createdHuman = $createdTs !== false ? date("M j, Y", $createdTs) : "";
$createdIso   = $createdTs !== false ? date("c", $createdTs) : "";
$liked        = (int) ($item["user_has_liked"] ?? 0) === 1;
$authorAvatar = $item["avatar_path"] ?? null;
?>
<li id="image-<?= $e((int) $item["id"]) ?>">
	<figure class="brutal-card bg-paper !p-0 overflow-hidden flex flex-col h-full">
		<a href="/image?id=<?= $e((int) $item["id"]) ?>" class="block aspect-square w-full bg-ink border-b-3 border-ink overflow-hidden group" aria-label="View details for snap by <?= $e($item["username"]) ?>">
			<img
				src="<?= $e($item["image_path"]) ?>"
				alt="Snap by <?= $e($item["username"]) ?>"
				loading="lazy"
				class="w-full h-full object-contain transition-transform group-hover:scale-105"
			>
		</a>

		<figcaption class="p-4 sm:p-5 flex flex-col gap-3 flex-1">
			<div class="flex items-center justify-between gap-3">
				<div class="flex items-center gap-2 min-w-0 flex-1">
					<?php if (is_string($authorAvatar) && $authorAvatar !== ""): ?>
						<img src="<?= $e($authorAvatar) ?>" alt="" loading="lazy" class="w-8 h-8 object-cover border-2 border-ink shrink-0">
					<?php else: ?>
						<div class="w-8 h-8 flex items-center justify-center bg-cyan border-2 border-ink font-display font-black text-xs uppercase shrink-0">
							<?= $e(mb_substr((string) $item["username"], 0, 1)) ?>
						</div>
					<?php endif; ?>
					<p class="font-display font-black text-sm uppercase truncate">
						@<?= $e($item["username"]) ?>
					</p>
				</div>
				<?php if ($createdIso !== ""): ?>
					<time datetime="<?= $e($createdIso) ?>" class="font-mono text-[0.7rem] uppercase tracking-wider opacity-60 shrink-0">
						<?= $e($createdHuman) ?>
					</time>
				<?php endif; ?>
			</div>

			<div class="flex items-center gap-2 mt-auto">
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

				<a
					href="/image?id=<?= $e((int) $item["id"]) ?>"
					class="brutal-tag bg-cyan inline-flex items-center gap-1.5 hover:bg-paper transition-colors"
					aria-label="View comments"
				>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5" aria-hidden="true">
						<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
					</svg>
					<?= $e((int) $item["comment_count"]) ?>
				</a>
			</div>
		</figcaption>
	</figure>
</li>
