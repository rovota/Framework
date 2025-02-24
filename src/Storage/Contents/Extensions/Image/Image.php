<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Contents\Extensions\Image;

use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Storage\Contents\FileProperties;
use Rovota\Framework\Storage\Interfaces\FileContent;
use Rovota\Framework\Support\Photo;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Validation\ValidationTools;
use Stringable;

class Image implements FileContent, Stringable
{

	public Photo|null $photo {
		get => $this->photo;
	}

	protected File $container;

	// -----------------

	public function __construct(mixed $contents, File $container)
	{
		$this->photo = new Photo($contents, [
			'mime_type' => $container->properties->mime_type,
		]);

		$this->container = $container;
	}

	public function __toString(): string
	{
		return (string) $this->photo;
	}

	// -----------------

	public static function accepts(mixed $data, FileProperties $properties): bool
	{
		return Str::containsAny($properties->mime_type, [
			'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/avif', 'image/webp'
		]);
	}

	// -----------------

	public function type(string $type): static
	{
		if (ValidationTools::mimeTypeExists($type)) {
			$this->photo->type($type);
			$this->container->properties->mime_type = $type;
			$this->container->modified = true;
		}
		return $this;
	}

	public function quality(int $quality): static
	{
		$this->photo->quality($quality);
		$this->container->modified = true;
		return $this;
	}

	// -----------------

	public function resize(int $width, int|null $height = null): Image
	{
		if ($this->photo->resize($width, $height) !== false) {
			$this->container->modified = true;
		}
		return $this;
	}

	public function crop(int $width, int|null $height = null, int|null $x = null, int|null $y = null): Image
	{
		if ($this->photo->crop($width, $height, $x, $y) !== false) {
			$this->container->modified = true;
		}
		return $this;
	}

	// -----------------

	public function filter(int $type, array|int|float|bool ...$args): Image
	{
		if ($this->photo->filter($type, ...$args) !== false) {
			$this->container->modified = true;
		}
		return $this;
	}

}