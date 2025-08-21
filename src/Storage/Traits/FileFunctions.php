<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Traits;

use Rovota\Framework\Storage\Contents\Directory;
use Rovota\Framework\Storage\Contents\Extensions\Text\Text;
use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Support\Str;

trait FileFunctions
{

	public function asHash(string $algo = 'sha256', bool $binary = false): string|null
	{
		if ($this->contents === null) {
			return null;
		}
		return hash($algo, $this->asString(), $binary);
	}

	public function asString(): string
	{
		return (string)($this->contents ?? '');
	}

	// -----------------

	public function checksum(array $config = []): string
	{
		return $this->properties->disk->checksum($this->location(), $config);
	}

	// -----------------

	public function write(mixed $content): static
	{
		$this->contents = $this->createContentInstance($content);
		$this->modified = true;
		return $this;
	}

	// -----------------

	public function compress(string|null $target = null): File|null
	{
		return $this->properties->disk->compress($this->location(), $target);
	}

	public function extract(string|null $target = null): Directory|null
	{
		return $this->properties->disk->extract($this->location(), $target);
	}

	// -----------------

	public function move(string $to, array $options = []): bool
	{
		$target = trim($to, '/') . '/' . $this->properties->name . '.' . $this->properties->extension;

		if ($this->properties->disk->move($this->location(), $target, $options)) {
			$this->properties->path = Str::before($target, '/');
			return true;
		}

		return false;
	}

	public function rename(string $name, array $options = []): bool
	{
		if (Str::containsNone($name, ['.'])) {
			$name = sprintf('%s.%s', $name, $this->properties->extension);
		}

		if ($this->properties->disk->rename($this->location(), $this->properties->path . '/' . $name, $options)) {
			$this->properties->name = Str::beforeLast($name, '.');
			$this->properties->extension = Str::afterLast($name, '.');
		}

		return false;
	}

	public function copy(string $to, array $options = []): bool
	{
		return $this->properties->disk->copy($this->location(), $to, $options);
	}

	// -----------------

	public function delete(): bool
	{
		return $this->properties->disk->delete($this->location());
	}

	public function clear(): static
	{
		$this->contents = null;
		$this->modified = true;
		return $this;
	}

	// -----------------

	public function prepend(string $content, bool $new_line = true): static
	{
		$new_line = empty($this->asString()) === false && $new_line === true;
		$this->contents = new Text(Str::finish($content, $new_line ? "\n" : '') . $this->asString());
		$this->modified = true;
		return $this;
	}

	public function append(string $content, bool $new_line = true): static
	{
		$new_line = empty($this->asString()) === false && $new_line === true;
		$this->contents = new Text(Str::finish($this->asString(), $new_line ? "\n" : '') . $content);
		$this->modified = true;
		return $this;
	}

	public function findAndReplace(array|string $search, array|string $replace, bool $count = false): static|int
	{
		$this->contents = new Text(str_replace($search, $replace, $this->asString(), $count));
		$this->modified = true;
		return $count ?: $this;
	}

	// -----------------

	public function isExtension(string $extension): bool
	{
		return $this->properties->extension === $extension;
	}

	public function isAnyExtension(array $extensions): bool
	{
		return array_any($extensions, fn($extension) => $this->isExtension($extension));
	}

	// -----------------

	public function isMimeType(string $mime_type): bool
	{
		return $this->properties->mime_type === $mime_type;
	}

	public function isAnyMimeType(array $mime_types): bool
	{
		return array_any($mime_types, fn($mime_type) => $this->isMimeType($mime_type));
	}

	// -----------------

	public function save(array $options = []): static
	{
		if ($this->modified) {
			$this->properties->disk->write($this->location(), $this->asString(), $options);
		}
		return $this;
	}

	// -----------------

	public function location(): string
	{
		return sprintf('%s/%s.%s', $this->properties->path, $this->properties->name, $this->properties->extension);
	}

}