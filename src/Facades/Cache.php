<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Caching\CacheManager;
use Rovota\Framework\Caching\CacheStore;
use Rovota\Framework\Caching\Enums\Driver;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Facade;

/**
 * @method static CacheStore store(string|null $name = null)
 * @method static CacheStore|null storeWithDriver(Driver $driver)
 * @method static CacheStore create(array $config, string|null $name = null)
 *
 * @method static Map all()
 * @method static bool has(string|array $key)
 * @method static bool missing(string|array $key)
 * @method static mixed get(string|array $key, mixed $default = null)
 * @method static mixed pull(string|array $key, mixed $default = null)
 * @method static mixed remember(string $key, callable $callback, int|null $retention = null)
 * @method static mixed rememberForever(string $key, callable $callback)
 * @method static void set(string|int|array $key, mixed $value = null, int|null $retention = null)
 * @method static void forever(string|int|array $key, mixed $value = null)
 * @method static void remove(string|array $key)
 * @method static void increment(string $key, int $step = 1)
 * @method static void decrement(string $key, int $step = 1)
 * @method static void flush()
 * @method static string|null lastModified()
 */
final class Cache extends Facade
{

	public static function service(): CacheManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return CacheManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'store' => 'get',
			'storeWithDriver' => 'getWithDriver',
			'create' => 'createStore',
			default => function (CacheManager $instance, string $method, array $parameters = []) {
				return $instance->get()->$method(...$parameters);
			},
		};
	}

}