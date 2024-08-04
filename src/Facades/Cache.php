<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Rovota\Framework\Caching\CacheManager;
use Rovota\Framework\Caching\Interfaces\CacheInterface;
use Rovota\Framework\Structures\Map;

final class Cache
{

	protected function __construct()
	{
	}

	// -----------------

	public static function store(string $name): CacheInterface|null
	{
		return CacheManager::getStore($name);
	}

	// -----------------

	public static function create(array $config, string|null $name = null): CacheInterface|null
	{
		return CacheManager::createStore($config, $name);
	}

	// -----------------

	public static function all(): Map
	{
		return CacheManager::getStore()?->all() ?? new Map();
	}

	// -----------------

	public static function set(string|int|array $key, mixed $value = null, int|null $retention = null): void
	{
		CacheManager::getStore()?->set($key, $value, $retention);
	}

	public static function forever(string|int|array $key, mixed $value = null): void
	{
		CacheManager::getStore()?->forever($key, $value);
	}

	// -----------------

	public static function has(string|array $key): bool
	{
		return CacheManager::getStore()?->has($key) ?? false;
	}

	public static function missing(string|array $key): bool
	{
		return CacheManager::getStore()?->missing($key) ?? true;
	}

	// -----------------

	public static function get(string|array $key, mixed $default = null): mixed
	{
		return CacheManager::getStore()?->get($key, $default) ?? $default;
	}

	public static function remove(string|array $key): void
	{
		CacheManager::getStore()?->remove($key);
	}

	// -----------------

	public static function pull(string|array $key, mixed $default = null): mixed
	{
		return CacheManager::getStore()?->pull($key, $default) ?? $default;
	}

	public static function remember(string $key, callable $callback, int|null $retention = null): mixed
	{
		return CacheManager::getStore()?->remember($key, $callback, $retention) ?? $callback;
	}

	public static function rememberForever(string $key, callable $callback): mixed
	{
		return CacheManager::getStore()?->remember($key, $callback, 31536000) ?? $callback;
	}

	// -----------------

	public static function increment(string $key, int $step = 1): void
	{
		CacheManager::getStore()?->increment($key, $step);
	}

	public static function decrement(string $key, int $step = 1): void
	{
		CacheManager::getStore()?->decrement($key, $step);
	}

	// -----------------

	public static function flush(): void
	{
		CacheManager::getStore()?->flush();
	}

	// -----------------

	public static function lastModified(): string|null
	{
		return CacheManager::getStore()?->lastModified();
	}

}