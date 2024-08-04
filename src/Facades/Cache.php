<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Rovota\Framework\Caching\CacheStore;
use Rovota\Framework\Caching\Caching;
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
		return Caching::get($name);
	}

	// -----------------

	public static function build(array $options, string|null $name = null): CacheInterface
	{
		return CacheStore::create($options, $name);
	}

	// -----------------

	public static function all(): Map
	{
		return Caching::get()?->all() ?? new Map();
	}

	// -----------------

	public static function set(string|int|array $key, mixed $value = null, int|null $retention = null): void
	{
		Caching::get()?->set($key, $value, $retention);
	}

	public static function forever(string|int|array $key, mixed $value = null): void
	{
		Caching::get()?->forever($key, $value);
	}

	// -----------------

	public static function has(string|array $key): bool
	{
		return Caching::get()?->has($key) ?? false;
	}

	public static function missing(string|array $key): bool
	{
		return Caching::get()?->missing($key) ?? true;
	}

	// -----------------

	public static function get(string|array $key, mixed $default = null): mixed
	{
		return Caching::get()?->get($key, $default) ?? $default;
	}

	public static function remove(string|array $key): void
	{
		Caching::get()?->remove($key);
	}

	// -----------------

	public static function pull(string|array $key, mixed $default = null): mixed
	{
		return Caching::get()?->pull($key, $default) ?? $default;
	}

	public static function remember(string $key, callable $callback, int|null $retention = null): mixed
	{
		return Caching::get()?->remember($key, $callback, $retention) ?? $callback;
	}

	public static function rememberForever(string $key, callable $callback): mixed
	{
		return Caching::get()?->remember($key, $callback, 31536000) ?? $callback;
	}

	// -----------------

	public static function increment(string $key, int $step = 1): void
	{
		Caching::get()?->increment($key, $step);
	}

	public static function decrement(string $key, int $step = 1): void
	{
		Caching::get()?->decrement($key, $step);
	}

	// -----------------

	public static function flush(): void
	{
		Caching::get()?->flush();
	}

	// -----------------

	public static function lastModified(): string|null
	{
		return Caching::get()?->lastModified();
	}

}