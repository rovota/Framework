<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Adapters;

use Rovota\Framework\Caching\Interfaces\CacheAdapterInterface;
use Rovota\Framework\Structures\Config;

class APCuAdapter implements CacheAdapterInterface
{

	protected string|null $last_modified = null;

	protected string|null $scope = null;

	// -----------------

	public function __construct(Config $parameters)
	{
		$this->scope = $parameters->get('scope');
	}

	// -----------------

	public function all(): array
	{
		return [];
	}

	// -----------------

	public function has(string $key): bool
	{
		return apcu_exists($this->getScopedKey($key));
	}

	public function set(string $key, mixed $value, int $retention): void
	{
		$this->last_modified = $key;
		apcu_store($this->getScopedKey($key), $value, $retention);
	}

	public function get(string $key): mixed
	{
		$key = $this->getScopedKey($key);
		return apcu_exists($key) ? apcu_fetch($key) : null;
	}

	public function remove(string $key): void
	{
		$this->last_modified = $key;
		apcu_delete($this->getScopedKey($key));
	}

	// -----------------

	public function increment(string $key, int $step = 1): void
	{
		$this->last_modified = $key;
		apcu_inc($this->getScopedKey($key), $step);
	}

	public function decrement(string $key, int $step = 1): void
	{
		$this->last_modified = $key;
		apcu_dec($this->getScopedKey($key), $step);
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

	// -----------------

	protected function getScopedKey(string $key): string
	{
		if ($this->scope === null || mb_strlen($this->scope) === 0) {
			return $key;
		}
		return sprintf('%s:%s', $this->scope, $key);
	}

}