<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Adapters;

use Rovota\Framework\Caching\Interfaces\CacheAdapterInterface;
use Rovota\Framework\Structures\Bucket;

class ArrayAdapter implements CacheAdapterInterface
{

	protected Bucket $storage;

	protected string|null $last_modified = null;

	// -----------------

	public function __construct()
	{
		$this->storage = new Bucket();
	}

	// -----------------

	public function all(): array
	{
		return $this->storage->all();
	}

	// -----------------

	public function has(string $key): bool
	{
		return $this->storage->has($key);
	}

	public function set(string $key, mixed $value, int $retention): void
	{
		$this->last_modified = $key;
		$this->storage->set($key, $value);
	}

	public function get(string $key): mixed
	{
		return $this->storage->get($key);
	}

	public function remove(string $key): void
	{
		$this->last_modified = $key;
		$this->storage->remove($key);
	}

	// -----------------

	public function increment(string $key, int $step = 1): void
	{
		$this->last_modified = $key;
		$this->storage->increment($key, $step);
	}

	public function decrement(string $key, int $step = 1): void
	{
		$this->last_modified = $key;
		$this->storage->increment($key, $step);
	}

	// -----------------

	public function flush(): void
	{
		$this->storage->flush();
	}

	// -----------------

	public function lastModified(): string|null
	{
		return $this->last_modified;
	}

}