<?php
/**
 * @var \Closure(mixed): string $e      Escape helper.
 * @var string                  $title  Page title.
 */
require __DIR__ . "/_header.php";
?>

<section class="text-center py-12 sm:py-20">
	<h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-900">
		<?= $e($title) ?>
	</h1>
	<p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
		Capture une photo, ajoute un overlay, partage ta création dans la galerie publique.
	</p>
	<div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
		<a href="/edit" class="rounded-md bg-indigo-600 px-6 py-3 text-white font-medium hover:bg-indigo-700 transition-colors">
			Commencer
		</a>
		<a href="/gallery" class="rounded-md border border-gray-300 bg-white px-6 py-3 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
			Voir la galerie
		</a>
	</div>
</section>

<?php require __DIR__ . "/_footer.php"; ?>
