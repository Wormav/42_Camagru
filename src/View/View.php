<?php

declare(strict_types=1);

namespace App\View;

class View
{
	private string $templatesDir;

	public function __construct(string $templatesDir)
	{
		$this->templatesDir = rtrim($templatesDir, "/") . "/";
	}

	// Load the specified template, passing the provided data as variables, and wrap it in the specified layout.
	public function render(string $template, array $data = [], ?string $layout = "appTemplate"): void
	{
		$templatePath = $this->templatesDir . $template . ".php";
		if (!file_exists($templatePath)) {
			throw new \RuntimeException("Template not found: " . $templatePath);
		}

		// Escape helper used by every template/layout to prevent XSS.
		$e = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");

		// Extract the data array to variables for use in the template.
		extract($data, EXTR_SKIP);

		// Capture the template output.
		ob_start();
		try {
			require $templatePath;
		} catch (\Throwable $error) {
			ob_end_clean();
			throw $error;
		}
		// Get the captured content and clean the buffer.
		$content = ob_get_clean();

		if ($layout === null) {
			echo $content;
			return;
		}

		// Make the $content variable available to the layout.
		$layoutPath = $this->templatesDir . "layouts/" . $layout . ".php";
		if (!file_exists($layoutPath)) {
			throw new \RuntimeException("Layout not found: " . $layoutPath);
		}

		require $layoutPath;
	}
}
