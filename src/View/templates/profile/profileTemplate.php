<?php
use App\Core\CsrfCore;

$memberSince = date("F j, Y", strtotime($currentUser["created_at"]));
$hasAvatar   = $currentUser["avatar_path"] !== null && $currentUser["avatar_path"] !== "";
$notifyOn    = (int) $currentUser["notify_comments"] === 1;
?>
<section class="max-w-xl mx-auto px-4 sm:px-6 py-16 sm:py-20">

	<header class="mb-8">
		<span class="brutal-tag bg-pink">★ Your profile</span>
		<h1 class="font-display font-black text-display-lg mt-4 leading-[0.95]">
			Hey, <span class="highlight"><?= $e($currentUser["username"]) ?></span>
		</h1>
		<p class="mt-4 text-sm font-mono">
			// Manage your account and your face.
		</p>
	</header>

	<div class="brutal-card bg-paper text-center mb-6">
		<p class="font-mono text-xs uppercase tracking-widest opacity-60 mb-5">// Avatar</p>

		<?php if ($hasAvatar): ?>
			<img
				src="<?= $e($currentUser["avatar_path"]) ?>"
				alt="Your avatar"
				class="w-32 h-32 object-cover border-3 border-ink shadow-brutal mx-auto"
			>
		<?php else: ?>
			<div class="w-32 h-32 flex items-center justify-center border-3 border-ink bg-cyan shadow-brutal mx-auto">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" class="w-14 h-14" aria-hidden="true">
					<circle cx="12" cy="8" r="4"/>
					<path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>
				</svg>
			</div>
		<?php endif; ?>

		<form method="POST" action="/profile/avatar" enctype="multipart/form-data" class="mt-6 text-left space-y-3">
			<?= CsrfCore::field() ?>

			<label for="avatar" class="brutal-label">Upload a new avatar</label>
			<input
				id="avatar"
				name="avatar"
				type="file"
				accept="image/jpeg,image/png,image/webp"
				required
				class="brutal-input file:mr-3 file:border-0 file:bg-ink file:text-paper file:font-display file:font-bold file:uppercase file:text-xs file:px-3 file:py-2 file:cursor-pointer"
			>
			<span class="brutal-help">JPEG, PNG or WebP · 5MB max</span>

			<button type="submit" class="btn-brutal w-full">
				Save avatar →
			</button>
		</form>
	</div>

	<div class="brutal-card bg-paper mb-6">
		<p class="font-mono text-xs uppercase tracking-widest opacity-60 mb-5">// Username</p>

		<form method="POST" action="/profile/username" novalidate class="space-y-3">
			<?= CsrfCore::field() ?>

			<label for="username" class="brutal-label">Username</label>
			<input
				id="username"
				name="username"
				type="text"
				required
				minlength="3"
				maxlength="20"
				value="<?= $e($currentUser["username"]) ?>"
				autocomplete="username"
				class="brutal-input"
			>
			<span class="brutal-help">3 to 20 chars · letters, digits and underscore</span>

			<button type="submit" class="btn-brutal btn-brutal--cyan w-full">
				Update username →
			</button>
		</form>
	</div>

	<div class="brutal-card bg-paper mb-6">
		<p class="font-mono text-xs uppercase tracking-widest opacity-60 mb-5">// Email</p>

		<form method="POST" action="/profile/email" novalidate class="space-y-3">
			<?= CsrfCore::field() ?>

			<label for="email" class="brutal-label">Email</label>
			<input
				id="email"
				name="email"
				type="email"
				required
				maxlength="255"
				value="<?= $e($currentUser["email"]) ?>"
				autocomplete="email"
				class="brutal-input"
			>

			<button type="submit" class="btn-brutal btn-brutal--cyan w-full">
				Update email →
			</button>
		</form>
	</div>

	<div class="brutal-card bg-paper mb-6">
		<p class="font-mono text-xs uppercase tracking-widest opacity-60 mb-5">// Password</p>

		<form method="POST" action="/profile/password" novalidate class="space-y-4">
			<?= CsrfCore::field() ?>

			<div>
				<label for="current_password" class="brutal-label">Current password</label>
				<input
					id="current_password"
					name="current_password"
					type="password"
					required
					autocomplete="current-password"
					class="brutal-input"
					placeholder="••••••••"
				>
			</div>

			<div>
				<label for="new_password" class="brutal-label">New password</label>
				<input
					id="new_password"
					name="new_password"
					type="password"
					required
					minlength="8"
					autocomplete="new-password"
					class="brutal-input"
					placeholder="••••••••"
				>
				<span class="brutal-help">8+ chars · 1 uppercase · 1 lowercase · 1 digit · 1 special</span>
			</div>

			<button type="submit" class="btn-brutal btn-brutal--cyan w-full">
				Update password →
			</button>
		</form>
	</div>

	<div class="brutal-card bg-paper mb-6">
		<p class="font-mono text-xs uppercase tracking-widest opacity-60 mb-5">// Preferences</p>

		<form method="POST" action="/profile/notifications" class="space-y-4">
			<?= CsrfCore::field() ?>

			<label class="flex items-start gap-3 cursor-pointer select-none">
				<input
					type="checkbox"
					name="notify_comments"
					value="1"
					<?= $notifyOn ? "checked" : "" ?>
					class="mt-0.5 w-5 h-5 border-3 border-ink accent-lime cursor-pointer"
				>
				<span>
					<span class="brutal-label !mb-0">Comment notifications</span>
					<span class="block mt-1 font-mono text-xs opacity-70">
						// receive an email when someone comments on your photos.
					</span>
				</span>
			</label>

			<button type="submit" class="btn-brutal btn-brutal--cyan w-full">
				Save preferences →
			</button>
		</form>
	</div>

	<div class="brutal-card bg-paper">
		<p class="font-mono text-xs uppercase tracking-widest opacity-60 mb-5">// Meta</p>

		<p class="brutal-label">Member since</p>
		<p class="font-mono text-sm"><?= $e($memberSince) ?></p>
	</div>

</section>
