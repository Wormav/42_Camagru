<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
	->in([
		__DIR__ . "/src",
		__DIR__ . "/public",
		__DIR__ . "/config",
	])
	->exclude("vendor")
	->exclude("node_modules");

return (new PhpCsFixer\Config())
	->setRiskyAllowed(true)
	->setIndent("\t")
	->setLineEnding("\n")
	->setRules([
		"@PSR12" => true,
		"declare_strict_types" => true,
		"no_unused_imports" => true,
		"ordered_imports" => ["sort_algorithm" => "alpha"],
		"array_syntax" => ["syntax" => "short"],
		"trailing_comma_in_multiline" => true,
		"no_trailing_whitespace" => true,
		"no_whitespace_in_blank_line" => true,
		"single_blank_line_at_eof" => true,
	])
	->setFinder($finder);
