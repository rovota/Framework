<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Traits;

use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Storage\Contents\Directory;
use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Structures\Sequence;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Validation\ValidationTools;
use Throwable;

trait DiskFunctions
{

	public function contents(string $location = '/'): Sequence
	{
		try {
			$result = $this->flysystem->listContents($location, false);
			return new Sequence($result->toArray());
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}

		return new Sequence();
	}

	public function files(string $location = '/'): Sequence
	{
		return $this->contents($location)->filter(function (mixed $item) {
			return $item instanceof FileAttributes;
		});
	}

	public function directories(string $location = '/'): Sequence
	{
		return $this->contents($location)->filter(function (mixed $item) {
			return $item instanceof DirectoryAttributes;
		});
	}

	// -----------------

	public function exists(string $location): bool
	{
		try {
			return $this->flysystem->has($location);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}

		return false;
	}

	public function missing(string $location): bool
	{
		try {
			return $this->flysystem->has($location) === false;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}

		return true;
	}

	// -----------------

	public function checksum(string $location, array $config = []): string
	{
		return $this->flysystem->checksum($location, $config);
	}

	// -----------------

	public function file(string $location, array $without = []): File|null
	{
		$fields = ['size', 'mime_type', 'last_modified'];

		foreach ($without as $field) {
			if (($key = array_search($field, $fields)) !== false) {
				unset($fields[$key]);
			}
		}

		return $this->retrieveFileWithData($location, $fields);
	}

	public function directory(string $location): Directory|null
	{
		return $this->retrieveDirectoryWithData($location);
	}

	// -----------------

	public function read(string $location): string|null
	{
		try {
			return $this->flysystem->read($location);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}

		return null;
	}

	public function write(string $location, string $contents): bool
	{
		try {
			$this->flysystem->write($location, $contents);
			return true;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return false;
	}

	public function writeStream(string $location, mixed $contents): bool
	{
		try {
			$this->flysystem->writeStream($location, $contents);
			return true;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return false;
	}

	// -----------------

	public function move(string $from, string $to): bool
	{
		try {
			$this->flysystem->move($from, $to);
			return true;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return false;
	}

	public function rename(string $location, string $name): bool
	{
		try {
			$target = str_replace(basename($location), $name, $location);
			$this->flysystem->move($location, $target);
			return true;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return false;
	}

	public function copy(string $from, string $to): bool
	{
		try {
			$this->flysystem->copy($from, $to);
			return true;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return false;
	}

	// -----------------

	public function lastModified(string $location): Moment|null
	{
		try {
			return new Moment($this->flysystem->lastModified($location));
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

	public function size(string $location): int
	{
		try {
			return $this->flysystem->fileSize($location);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return 0;
	}

	public function mimeType(string $location): string|null
	{
		try {
			$extension = pathinfo($location, PATHINFO_EXTENSION);
			return ValidationTools::sanitizeMimeType($extension, $this->flysystem->mimeType($location));
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

	// -----------------

	public function delete(string $location): bool
	{
		try {
			$this->flysystem->delete($location);
			return true;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return false;
	}

	public function deleteDirectory(string $location): bool
	{
		try {
			$this->flysystem->deleteDirectory($location);
			return true;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return false;
	}

	public function clear(string $location): bool
	{
		try {
			$this->flysystem->write($location, '');
			return true;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return false;
	}

	public function clearDirectory(string $location): bool
	{
		try {
			$contents = $this->flysystem->listContents($location);
			foreach ($contents as $item) {
				match($item['type']) {
					'file' => $this->flysystem->delete($item['path']),
					'dir' => $this->flysystem->deleteDirectory($item['path']),
				};
			}
			return true;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return false;
	}

	// -----------------

	public function prepend(string $location, string $contents, bool $new_line = true): void
	{
		if ($this->missing($location)) {
			$this->write($location, $contents);
		} else {
			$this->retrieveFileWithData($location)?->prepend($contents, $new_line)->save();
		}
	}

	public function append(string $location, string $contents, bool $new_line = true): void
	{
		if ($this->missing($location)) {
			$this->write($location, $contents);
		} else {
			$this->retrieveFileWithData($location)?->append($contents, $new_line)->save();
		}
	}

	// -----------------

	public function isExtension(string $location, string $extension): bool
	{
		return $this->retrieveFileWithData($location)?->isExtension($extension) ?? false;
	}

	public function isAnyExtension(string $location, array $extensions): bool
	{
		return $this->retrieveFileWithData($location)?->isAnyExtension($extensions) ?? false;
	}

	// -----------------

	public function isMimeType(string $location, string $mime_type): bool
	{
		return $this->retrieveFileWithData($location, ['mime_type'])?->isMimeType($mime_type) ?? false;
	}

	public function isAnyMimeType(string $location, array $mime_types): bool
	{
		return $this->retrieveFileWithData($location, ['mime_type'])?->isAnyMimeType($mime_types) ?? false;
	}

	// -----------------

	public function isEqual(string $first, string $second): bool
	{
		$first = $this->read($first);
		$second = $this->read($second);

		if ($first === null || $second === null) {
			return false;
		}

		return hash_equals($first, $second);
	}

	// -----------------

	protected function retrieveFileWithData(string $location, array $data = []): File|null
	{
		if ($this->missing($location)) {
			return null;
		}

		$contents = $this->read($location);
		$extension = pathinfo($location, PATHINFO_EXTENSION);

		$properties = [
			'name' => basename($location),
			'path' => str_replace(basename($location), '', $location),
			'extension' => $extension,
			'disk' => $this,
		];

		foreach ($data as $name) {
			$properties[$name] = match($name) {
				'size' => $this->size($location),
				'mime_type' => $this->mimeType($location),
				'last_modified' => $this->lastModified($location),
			};
		}

		return new File($contents, $properties);
	}

	protected function retrieveDirectoryWithData(string $location): Directory|null
	{
		if ($this->missing($location)) {
			return null;
		}

		return new Directory([
			'name' => basename($location),
			'path' => str_replace(basename($location), '', $location),
			'disk' => $this,
		]);
	}

}