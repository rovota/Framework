<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching;

use Rovota\Framework\Caching\Drivers\APCuDriver;
use Rovota\Framework\Caching\Drivers\ArrayDriver;
use Rovota\Framework\Caching\Drivers\RedisDriver;
use Rovota\Framework\Caching\Enums\Driver;
use Rovota\Framework\Caching\Exceptions\CacheMisconfigurationException;
use Rovota\Framework\Caching\Exceptions\MissingCacheStoreException;
use Rovota\Framework\Caching\Interfaces\CacheInterface;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Internal;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class CacheManager
{

	/**
	 * @var Map<string, CacheInterface>
	 */
	protected static Map $stores;

	protected static string $default;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		self::$stores = new Map();

		$config = require Internal::projectFile('config/caching.php');

		foreach ($config['stores'] as $name => $options) {
			$store =  self::build($name, $options);
			if ($store instanceof CacheInterface) {
				self::$stores->set($name, $store);
			}
		}

		self::setDefault($config['default']);
	}

	// -----------------

	public static function createStore(array $options, string|null $name = null): CacheInterface|null
	{
		return self::build($name ?? Str::random(20), $options);
	}

	// -----------------

	public static function hasStore(string $name): bool
	{
		return isset(self::$stores[$name]);
	}

	public static function addStore(string $name, array $config): void
	{
		$store = self::build($name, $config);

		if ($store instanceof CacheInterface) {
			self::$stores[$name] = $store;
		}
	}

	public static function getStore(string|null $name = null): CacheInterface|null
	{
		if ($name === null) {
			$name = self::$default;
		}

		return self::$stores[$name] ?? null;
	}

	// -----------------

	/**
	 * @returns Map<string, CacheInterface>
	 */
	public static function getStores(): Map
	{
		return self::$stores;
	}

	// -----------------

	public static function setDefault(string $name): void
	{
		if (isset(self::$stores[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingCacheStoreException("Undefined caches cannot be set as default: '$name'."));
		}
		self::$default = $name;
	}

	public static function getDefault(): string
	{
		return self::$default;
	}

	// -----------------

	protected static function build(string $name, array $config): CacheInterface|null
	{
		$config = new CacheStoreConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException("The selected driver '{$config->get('driver')}' is not supported."));
			return null;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new CacheMisconfigurationException("The cache '$name' cannot be used due to a configuration issue."));
			return null;
		}

		return match($config->driver) {
			Driver::APCu => new APCuDriver($name, $config),
			Driver::Array => new ArrayDriver($name, $config),
			Driver::Redis => new RedisDriver($name, $config),
			default => null,
		};
	}

}