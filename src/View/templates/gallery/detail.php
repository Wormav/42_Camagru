<?php
/**
 * Image detail — single snap with likes and comments.
 *
 * @var \Closure(mixed): string $e          Escape helper.
 * @var string                  $title      Page title.
 * @var array{
 *     id:int,
 *     user_id:int,
 *     image_path:string,
 *     overlay_used:?string,
 *     created_at:string,
 *     username:string,
 *     like_count:int,
 *     comment_count:int,
 *     user_has_liked:int
 * } $item
 * @var array<int,array{id:int,image_id:int,user_id:int,content:string,created_at:string,username:string}> $comments
 * @var bool      $isAuth
 * @var int|null  $currentUserId
 */

use App\Core\Csrf;

$liked       = (int) ($item["user_has_liked"] ?? 0) === 1;
$createdTs   = strtotime((string) $item["created_at"]);
$createdIso  = $createdTs !== false ? date("c",          $createdTs) : "";
$createdHuman = $createdTs !== false ? date("M j, Y · H:i", $createdTs) : "";
?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-12" data-gallery-grid>

	<nav class="mb-6 flex items-center justify-between gap-3">
		<a href="/gallery" class="btn-brutal btn-brutal--white !py-2 !px-3 text-sm">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4" aria-hidden="true">
				<polyline points="15 18 9 12 15 6"/>
			</svg>
			Back to gallery
		</a>
		<span class="font-mono text-xs uppercase tracking-widest opacity-60">// snap #<?= $e((int) $item["id"]) ?></span>
	</nav>

	<div class="grid grid-cols-1 lg:grid-cols-[1.4fr_1fr] gap-6 lg:gap-8 items-start">

		<!-- LEFT — image + meta + like -->
		<article class="brutal-card bg-paper !p-0 overflow-hidden">
			<div class="aspect-square w-full bg-ink border-b-3 border-ink overflow-hidden">
				<img
					src="<?= $e($item["image_path"]) ?>"
					alt="Snap by <?= $e($item["username"]) ?>"
					class="w-full h-full object-cover"
				>
			</div>

			<div class="p-5 sm:p-6 flex flex-col gap-4">
				<div class="flex items-center justify-between gap-3">
					<div class="flex items-center gap-3 min-w-0 flex-1">
						<?php $authorAvatar = $item["avatar_path"] ?? null; ?>
						<?php if (is_string($authorAvatar) && $authorAvatar !== ""): ?>
							<img src="<?= $e($authorAvatar) ?>" alt="" loading="lazy" class="w-12 h-12 object-cover border-3 border-ink shadow-brutal-sm shrink-0">
						<?php else: ?>
							<div class="w-12 h-12 flex items-center justify-center bg-cyan border-3 border-ink shadow-brutal-sm font-display font-black text-lg uppercase shrink-0">
								<?= $e(mb_substr((string) $item["username"], 0, 1)) ?>
							</div>
						<?php endif; ?>
						<p class="font-display font-black text-lg uppercase truncate">
							@<?= $e($item["username"]) ?>
						</p>
					</div>
					<?php if ($createdIso !== ""): ?>
						<time datetime="<?= $e($createdIso) ?>" class="font-mono text-xs uppercase tracking-wider opacity-60 shrink-0">
							<?= $e($createdHuman) ?>
						</time>
					<?php endif; ?>
				</div>

				<div class="flex items-center gap-2">
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
						<a href="/login" class="brutal-tag bg-paper inline-flex items-center gap-1.5" title="Sign in to like">
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
						<span data-comment-count><?= $e((int) $item["comment_count"]) ?></span>
					</span>
				</div>
			</div>
		</article>

		<!-- RIGHT — comments -->
		<aside class="brutal-card bg-white !p-5 sm:!p-6 flex flex-col gap-5 lg:sticky lg:top-6 lg:max-h-[calc(100vh-3rem)]">

			<div class="flex items-center justify-between gap-3">
				<p class="font-display font-black text-sm uppercase">Comments</p>
				<span class="brutal-tag bg-cyan" data-comment-count><?= $e((int) $item["comment_count"]) ?></span>
			</div>

			<div
				class="border-3 border-ink bg-paper p-6 text-center<?= $comments === [] ? "" : " hidden" ?>"
				data-comments-empty
			>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-10 h-10 mx-auto opacity-60" aria-hidden="true">
					<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
				</svg>
				<p class="mt-3 font-display font-black text-sm uppercase">No comments yet</p>
				<p class="mt-2 font-mono text-xs opacity-60">// be the first.</p>
			</div>

			<ul
				class="flex flex-col gap-3 overflow-y-auto pr-1 flex-1 min-h-0<?= $comments === [] ? " hidden" : "" ?>"
				data-comments-list
			>
				<?php foreach ($comments as $comment): ?>
					<?php
					$cTs    = strtotime((string) $comment["created_at"]);
					$cIso   = $cTs !== false ? date("c",          $cTs) : "";
					$cHuman = $cTs !== false ? date("M j, H:i",   $cTs) : "";
					$isMine = $isAuth && $currentUserId !== null && (int) $comment["user_id"] === $currentUserId;
					?>
					<li class="border-3 border-ink bg-paper p-3 shadow-brutal-sm" data-comment-item data-comment-id="<?= $e((int) $comment["id"]) ?>">
						<div class="flex items-center justify-between gap-2 mb-1.5">
							<div class="flex items-center gap-2 flex-1 min-w-0">
								<?php $commenterAvatar = $comment["avatar_path"] ?? null; ?>
								<?php if (is_string($commenterAvatar) && $commenterAvatar !== ""): ?>
									<img src="<?= $e($commenterAvatar) ?>" alt="" loading="lazy" class="w-7 h-7 object-cover border-2 border-ink shrink-0">
								<?php else: ?>
									<div class="w-7 h-7 flex items-center justify-center bg-cyan border-2 border-ink font-display font-black text-[0.65rem] uppercase shrink-0">
										<?= $e(mb_substr((string) $comment["username"], 0, 1)) ?>
									</div>
								<?php endif; ?>
								<p class="font-display font-black text-xs uppercase truncate min-w-0">
									@<?= $e($comment["username"]) ?>
								</p>
							</div>

							<div class="flex items-center gap-2 shrink-0">
								<?php if ($cIso !== ""): ?>
									<time datetime="<?= $e($cIso) ?>" class="font-mono text-[0.65rem] uppercase tracking-wider opacity-60">
										<?= $e($cHuman) ?>
									</time>
								<?php endif; ?>

								<?php if ($isMine): ?>
									<form method="POST" action="/comments/delete" data-delete-comment-form>
										<?= Csrf::field() ?>
										<input type="hidden" name="comment_id" value="<?= $e((int) $comment["id"]) ?>">
										<button
											type="submit"
											class="w-5 h-5 flex items-center justify-center border-2 border-ink bg-coral hover:bg-red transition-colors text-paper opacity-70 hover:opacity-100"
											aria-label="Delete comment"
											title="Delete"
										>
											<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3" aria-hidden="true">
												<line x1="18" y1="6" x2="6" y2="18"/>
												<line x1="6" y1="6" x2="18" y2="18"/>
											</svg>
										</button>
									</form>
								<?php endif; ?>
							</div>
						</div>

						<p class="font-sans text-sm whitespace-pre-wrap break-words"><?= $e(trim((string) $comment["content"])) ?></p>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ($isAuth): ?>
				<form method="POST" action="/comments" class="flex flex-col gap-2" data-comment-form>
					<?= Csrf::field() ?>
					<input type="hidden" name="image_id" value="<?= $e((int) $item["id"]) ?>">
					<label class="sr-only" for="comment-input">Add a comment</label>
					<textarea
						id="comment-input"
						name="content"
						rows="3"
						maxlength="500"
						required
						placeholder="Drop a comment…"
						class="brutal-input w-full resize-y text-sm"
					></textarea>
					<button type="submit" class="btn-brutal btn-brutal--cyan !py-2 text-sm self-end">
						Post comment
					</button>
				</form>
			<?php else: ?>
				<a href="/login" class="btn-brutal btn-brutal--white text-center !py-2 text-sm">
					Sign in to comment
				</a>
			<?php endif; ?>
		</aside>
	</div>

	<?php if ($isAuth): ?>
		<template data-comment-template>
			<li class="border-3 border-ink bg-paper p-3 shadow-brutal-sm" data-comment-item>
				<div class="flex items-center justify-between gap-2 mb-1.5">
					<div class="flex items-center gap-2 flex-1 min-w-0">
						<img data-tpl-avatar alt="" loading="lazy" class="w-7 h-7 object-cover border-2 border-ink shrink-0 hidden">
						<div data-tpl-avatar-fallback class="w-7 h-7 flex items-center justify-center bg-cyan border-2 border-ink font-display font-black text-[0.65rem] uppercase shrink-0"></div>
						<p class="font-display font-black text-xs uppercase truncate min-w-0">
							@<span data-tpl-username></span>
						</p>
					</div>

					<div class="flex items-center gap-2 shrink-0">
						<time data-tpl-time class="font-mono text-[0.65rem] uppercase tracking-wider opacity-60"></time>

						<form method="POST" action="/comments/delete" data-delete-comment-form data-tpl-delete>
							<input type="hidden" name="csrf_token" data-tpl-csrf>
							<input type="hidden" name="comment_id" data-tpl-comment-id>
							<button
								type="submit"
								class="w-5 h-5 flex items-center justify-center border-2 border-ink bg-coral hover:bg-red transition-colors text-paper opacity-70 hover:opacity-100"
								aria-label="Delete comment"
								title="Delete"
							>
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3" aria-hidden="true">
									<line x1="18" y1="6" x2="6" y2="18"/>
									<line x1="6" y1="6" x2="18" y2="18"/>
								</svg>
							</button>
						</form>
					</div>
				</div>

				<p data-tpl-content class="font-sans text-sm whitespace-pre-wrap break-words"></p>
			</li>
		</template>
	<?php endif; ?>
</section>
