<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Interfaces;

use Rovota\Framework\Caching\CacheStoreConfig;
use Rovota\Framework\Structures\Map;

interface CacheInterface
{

	public function __toString(): string;

	// -----------------

	public static function create(array $options, string|null $name = null): CacheInterface;

	// -----------------

	public function isDefault(): bool;

	// -----------------

	public function name(): string;

	public function config(): CacheStoreConfig;

	public function adapter(): CacheAdapterInterface;

	// -----------------

	public function all(): Map;

	// -----------------

	public function set(string|int|array $key, mixed $value = null, int|null $retention = null): void;

	public function forever(string|int|array $key, mixed $value = null): void;

	// -----------------

	public function has(string|array $key): bool;

	public function missing(string|array $key): bool;

	// -----------------

	public function get(string|array $key, mixed $default = null): mixed;

	public function remove(string|array $key): void;

	// -----------------

	/**
	 * Returns the cached value (or the default), and then removes it from cache if present.
	 */
	public function pull(string|array $key, mixed $default = null): mixed;

	/**
	 * When cached, it'll return the cached value. Otherwise, it'll cache the result of the callback and return that instead.
	 */
	public function remember(string $key, callable $callback, int|null $retention = null): mixed;

	/**
	 * When cached, it'll return the cached value. Otherwise, it'll cache the result of the callback and return that instead.
	 */
	public function rememberForever(string $key, callable $callback): mixed;

	// -----------------

	public function increment(string $key, int $step = 1): void;

	public function decrement(string $key, int $step = 1): void;

	// -----------------

	public function flush(): void;

	// -----------------

	public function lastModified(): string|null;

}