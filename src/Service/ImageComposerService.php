<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\FilenameCore;

class ImageComposerService
{
	public function __construct(private int $jpegQuality = 90)
	{
	}

	public function merge(string $basePath, ?string $overlayPath, string $snapsDirectory): ?string
	{

		if (!is_file($basePath) || !is_readable($basePath)) {
			return null;
		}

		$base = imagecreatefromjpeg($basePath);
		if (!$base) {
			return null;
		}

		if (!is_dir($snapsDirectory) || !is_writable($snapsDirectory)) {
			return null;
		}

		if ($overlayPath !== null) {
			if (!is_file($overlayPath) || !is_readable($overlayPath)) {
				return null;
			}
			$overlay = imagecreatefrompng($overlayPath);
			if (!$overlay) {
				return null;
			}

			imagealphablending($overlay, false);
			imagesavealpha($overlay, true);
			imagealphablending($base, true);

			imagecopyresampled(
				$base,
				$overlay,
				0,
				0,
				0,
				0,
				imagesx($base),
				imagesy($base),
				imagesx($overlay),
				imagesy($overlay),
			);
		}


		$fileName =  FilenameCore::randomized("jpg", "snap_");
		$finalPath = $snapsDirectory . DIRECTORY_SEPARATOR . $fileName;
		$tempPath = $finalPath . ".tmp";

		if (!imagejpeg($base, $tempPath, $this->jpegQuality)) {
			return null;
		}

		if (!rename($tempPath, $finalPath)) {
			@unlink($tempPath);
			return null;
		}
		return $fileName;
	}

}
