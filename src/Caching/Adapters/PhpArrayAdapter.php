<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Adapters;

use Rovota\Framework\Caching\Interfaces\CacheAdapterInterface;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Structures\Config;

class PhpArrayAdapter implements CacheAdapterInterface
{

	protected Bucket $storage;

	protected string|null $last_modified = null;

	protected string|null $scope = null;

	// -----------------

	public function __construct(Config $parameters)
	{
		$this->storage = new Bucket();

		$this->scope = $parameters->get('scope');
	}

	// -----------------

	public function all(): array
	{
		return $this->storage->all();
	}

	// -----------------

	public function has(string $key): bool
	{
		return $this->storage->has($this->getScopedKey($key));
	}

	public function set(string $key, mixed $value, int $retention): void
	{
		$this->last_modified = $key;
		$this->storage->set($this->getScopedKey($key), $value);
	}

	public function get(string $key): mixed
	{
		return $this->storage->get($this->getScopedKey($key));
	}

	public function remove(string $key): void
	{
		$this->last_modified = $key;
		$this->storage->remove($this->getScopedKey($key));
	}

	// -----------------

	public function increment(string $key, int $step = 1): void
	{
		$this->last_modified = $key;
		$this->storage->increment($this->getScopedKey($key), $step);
	}

	public function decrement(string $key, int $step = 1): void
	{
		$this->last_modified = $key;
		$this->storage->increment($this->getScopedKey($key), $step);
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

	// -----------------

	protected function getScopedKey(string $key): string
	{
		if ($this->scope === null || mb_strlen($this->scope) === 0) {
			return $key;
		}
		return sprintf('%s:%s', $this->scope, $key);
	}

}