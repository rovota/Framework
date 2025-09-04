<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Traits;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Storage\Contents\Directory;
use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Support\Str;
use SplFileInfo;
use Throwable;
use ZipArchive;

trait CompressionFunctions
{

	public function compress(string $source, string|null $target = null): File|null
	{
		$archive = new ZipArchive();
		$archive_name = Str::random(60) . '.zip';

		$source_type = $this->getCompressionSourceType($source);
		$source = $this->getCompressionSource($source);

		if ($source_type === null) {
			return null;
		}

		if ($archive->open($this->config->root . '/' . $archive_name, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {

			if ($source_type === 'directory') {
				/** @var SplFileInfo[] $files */
				$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::LEAVES_ONLY);

				foreach ($files as $file) {
					if ($file->isDir() === false) {
						$archive->addFile($file->getRealPath(), substr($file->getRealPath(), mb_strlen($source) + 1));
					}
				}
			}

			if ($source_type === 'file') {
				$archive->addFile($source, basename($source));
			}

		} else {
			return null;
		}

		if ($archive->close()) {
			try {
				if ($target !== null) {
					$this->flysystem->move($archive_name, $target);
				}
				return $this->file($target ?? $archive_name);
			} catch (Throwable $throwable) {
				ExceptionHandler::logThrowable($throwable);
			}
		}

		return null;
	}

	public function extract(string $source, string|null $target = null): Directory|null
	{
		$archive = new ZipArchive();

		$source = $this->getCompressionSource($source);

		if ($archive->open($source)) {
			if ($archive->extractTo($this->getCompressionTarget($target, $source))) {
				$archive->close();

				try {
					return $this->directory(mb_trim($target ?? '/', '/'));
				} catch (Throwable $throwable) {
					ExceptionHandler::logThrowable($throwable);
				}
			}
		}

		return null;
	}

	// -----------------

	protected function getCompressionTarget(string|null $target, string $source): string
	{
		return $target !== null ? getcwd() . '/' . $this->config->root . '/' . $target : str_replace(basename($source), '', $source);
	}

	protected function getCompressionSource(string $source): string
	{
		return mb_trim(getcwd() . '/' . $this->config->root . '/' . $source, '/');
	}

	protected function getCompressionSourceType(string $source): string|null
	{
		try {
			return match (true) {
				$this->flysystem->directoryExists($source) => 'directory',
				$this->flysystem->fileExists($source) => 'file',
				default => null,
			};
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

}