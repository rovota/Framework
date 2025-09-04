<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Request;

use Rovota\Framework\Facades\Storage;
use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Storage\Contents\FileProperties;
use Rovota\Framework\Storage\Disk;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Validation\ValidationTools;
use SplFileInfo;

class UploadedFile
{

	protected int $size;
	protected string $original_name;
	protected string $provisional_name;

	// -----------------

	public File|null $source = null;

	// -----------------

	public FileProperties|null $properties {
		get => $this->source?->properties;
	}

	// -----------------

	public function __construct(string $name, string $temp_name)
	{
		$source = new SplFileInfo($temp_name);

		$this->size = $source->getSize();

		$this->provisional_name = trim($temp_name);
		$this->original_name = basename(mb_trim($name));

		if (is_uploaded_file($source->getPathname()) && $source->getSize() > 0) {
			$file = $this->getFileInstanceFromResource();
			if ($file instanceof File) {
				$this->source = $file;
			}
		}
	}

	// -----------------

	public function store(string $path, string|null $name = null, Disk|string|null $disk = null, array $options = []): bool
	{
		if ($this->source === null) {
			return false;
		}

		$this->properties->path = $path;
		$this->properties->disk = $disk instanceof Disk ? $disk : Storage::disk($disk);

		if ($name !== null) {
			$this->withName($name);
		}

		if ($this->source->save($options)) {
			return true;
		}

		return false;
	}

	// -----------------

	public function withName(string $name): UploadedFile
	{
		if (Str::containsNone($name, ['.'])) {
			$this->properties->name = trim($name);
			return $this;
		}

		$this->properties->name = Str::beforeLast($name, '.');
		$this->properties->extension = Str::afterLast($name, '.');

		return $this;
	}

	// -----------------

	protected function getFileInstanceFromResource(): File|null
	{
		$handle = fopen($this->provisional_name, 'r');
		$contents = fread($handle, filesize($this->provisional_name));

		if ($contents !== false) {

			fclose($handle);

			$mime_type = $this->getMimeTypeForFile($this->provisional_name);

			$extension = pathinfo($this->original_name, PATHINFO_EXTENSION);
			$extension = ValidationTools::sanitizeExtension($mime_type, $extension);

			if ($extension !== null) {
				$name = mb_trim(Str::remove($this->original_name, $extension), '.');

				return new File($contents, [
					'name' => $name,
					'path' => '/',
					'extension' => $extension,
					'size' => $this->size,
					'mime_type' => $mime_type,
					'last_modified' => now(),
				], true);
			}
		}

		return null;
	}

	protected function getMimeTypeForFile(string $target): string
	{
		$info = finfo_open(FILEINFO_MIME_TYPE);
		if ($info !== false) {
			$mime_type = finfo_file($info, $target);
			finfo_close($info);
		} else {
			$mime_type = 'application/octet-stream';
		}
		return $mime_type;
	}

}