<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Adapters;

use Rovota\Framework\Caching\Interfaces\CacheAdapterInterface;

class APCuAdapter implements CacheAdapterInterface
{

	protected string|null $last_modified = null;

	// -----------------

	public function all(): array
	{
		return [];
	}

	// -----------------

	public function has(string $key): bool
	{
		return apcu_exists($key);
	}

	public function set(string $key, mixed $value, int $retention): void
	{
		$this->last_modified = $key;
		apcu_store($key, $value, $retention);
	}

	public function get(string $key): mixed
	{
		return apcu_exists($key) ? apcu_fetch($key) : null;
	}

	public function remove(string $key): void
	{
		$this->last_modified = $key;
		apcu_delete($key);
	}

	// -----------------

	public function increment(string $key, int $step = 1): void
	{
		$this->last_modified = $key;
		apcu_inc($key, $step);
	}

	public function decrement(string $key, int $step = 1): void
	{
		$this->last_modified = $key;
		apcu_dec($key, $step);
	}

	// -----------------

	public function flush(): void
	{
		apcu_clear_cache();
	}

	// -----------------

	public function lastModified(): string|null
	{
		return $this->last_modified;
	}

}