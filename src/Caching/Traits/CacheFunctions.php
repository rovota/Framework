<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Traits;

use Rovota\Framework\Structures\Map;

trait CacheFunctions
{

	public function all(): Map
	{
		return new Map($this->adapter->all());
	}

	// -----------------

	public function has(string|array $key): bool
	{
		foreach (is_array($key) ? $key : [$key] as $key) {
			if ($this->adapter->has($key) === false) {
				return false;
			}
		}

		return true;
	}

	public function missing(string|array $key): bool
	{
		foreach (is_array($key) ? $key : [$key] as $key) {
			if ($this->adapter->has($key) === true) {
				return false;
			}
		}

		return true;
	}

	// -----------------

	public function get(string|array $key, mixed $default = null): mixed
	{
		if (is_array($key)) {
			$result = [];
			foreach ($key as $entry) {
				$result[$entry] = $this->adapter->get($entry) ?? ($default[$entry] ?? null);
			}
		} else {
			$result = $this->adapter->get($key) ?? $default;
		}

		return $result;
	}

	/**
	 * Returns the cached value (or the default), and then removes it from cache if present.
	 */
	public function pull(string|array $key, mixed $default = null): mixed
	{
		if (is_array($key)) {
			$result = [];
			foreach ($key as $entry) {
				$result[$entry] = $this->get($entry) ?? ($default[$entry] ?? null);
				$this->remove($entry);
			}
		} else {
			$result = $this->adapter->get($key) ?? $default;
			$this->remove($key);
		}

		return $result;
	}

	/**
	 * When cached, it'll return the cached value. Otherwise, it'll cache the result of the callback and return that instead.
	 */
	public function remember(string $key, callable $callback, int|null $retention = null): mixed
	{
		if ($this->has($key)) {
			return $this->get($key);
		}

		$value = $callback();
		$this->set($key, $value, $retention);
		return $value;
	}

	/**
	 * When cached, it'll return the cached value. Otherwise, it'll cache the result of the callback and return that instead.
	 */
	public function rememberForever(string $key, callable $callback): mixed
	{
		return $this->remember($key, $callback, 31536000);
	}

	// -----------------

	public function set(string|int|array $key, mixed $value = null, int|null $retention = null): void
	{
		foreach (is_array($key) ? $key : [$key => $value] as $entry => $value) {
			$this->adapter->set($entry, $value, $this->getRetentionPeriod($retention));
		}
	}

	public function forever(string|int|array $key, mixed $value = null): void
	{
		$this->set($key, $value, 31536000);
	}

	public function remove(string|array $key): void
	{
		foreach (is_array($key) ? $key : [$key] as $entry) {
			$this->adapter->remove($entry);
		}
	}

	// -----------------

	public function increment(string $key, int $step = 1): void
	{
		$this->adapter->increment($key, $step);
	}

	public function decrement(string $key, int $step = 1): void
	{
		$this->adapter->decrement($key, $step);
	}

	// -----------------

	public function flush(): void
	{
		$this->adapter->flush();
	}

	// -----------------

	public function lastModified(): string|null
	{
		return $this->adapter->lastModified();
	}

}