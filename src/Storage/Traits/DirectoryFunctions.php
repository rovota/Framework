<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Traits;

use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Structures\Sequence;
use Rovota\Framework\Support\Str;

trait DirectoryFunctions
{

	public function contents(): Sequence
	{
		return $this->properties->disk->contents($this->location());
	}

	public function files(): Sequence
	{
		return $this->properties->disk->files($this->location());
	}

	public function directories(): Sequence
	{
		return $this->properties->disk->directories($this->location());
	}

	// -----------------

	public function exists(string $location): bool
	{
		return $this->properties->disk->exists($this->location() . '/' . $location);
	}

	public function missing(string $location): bool
	{
		return $this->properties->disk->missing($this->location() . '/' . $location);
	}

	// -----------------

	public function checksum(string $location, array $config = []): string
	{
		return $this->properties->disk->checksum($this->location() . '/' . $location, $config);
	}

	// -----------------

	public function compress(string|null $target = null): File|null
	{
		return $this->properties->disk->compress($this->location(), $target);
	}

	// -----------------

	public function move(string $to): bool
	{
		$target = mb_trim($to, '/') . '/' . $this->properties->name;

		if ($this->properties->disk->move($this->location(), $target)) {
			$this->properties->path = Str::before($target, '/');
			return true;
		}

		return false;
	}

	public function rename(string $name): bool
	{
		if ($this->properties->disk->rename($this->location(), $this->properties->path . '/' . $name)) {
			$this->properties->name = $name;
		}

		return false;
	}

	public function copy(string $to): bool
	{
		return $this->properties->disk->copy($this->location(), $to);
	}

	// -----------------

	public function delete(): bool
	{
		return $this->properties->disk->deleteDirectory($this->location());
	}

	public function clear(): bool
	{
		return $this->properties->disk->clearDirectory($this->location());
	}

	// -----------------

	public function location(): string
	{
		return sprintf('%s/%s', $this->properties->path, $this->properties->name);
	}

}