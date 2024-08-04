<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching;

use Rovota\Framework\Caching\Drivers\PhpArray;
use Rovota\Framework\Caching\Enums\Driver;
use Rovota\Framework\Caching\Exceptions\CacheMisconfigurationException;
use Rovota\Framework\Caching\Exceptions\MissingCacheStoreException;
use Rovota\Framework\Caching\Interfaces\CacheInterface;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Support\Internal;

final class Caching
{

	/**
	 * @var array<string, CacheInterface>
	 */
	protected static array $stores = [];

	protected static string $default;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 */
	public static function initialize(): void
	{
		$config = require Internal::projectFile('config/caching.php');

		foreach ($config['stores'] as $name => $options) {
			$store =  self::build($name, $options);
			if ($store instanceof CacheInterface) {
				self::$stores[$name] = $store;
			}
		}

		self::setDefault($config['default']);
	}

	// -----------------

	public static function build(string $name, array $config): CacheInterface|null
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
//			Driver::APCu => new Discord($name, $config),
			Driver::Array => new PhpArray($name, $config),
//			Driver::Redis => new Stack($name, $config),
			default => null,
		};
	}

	// -----------------

	public static function has(string $name): bool
	{
		return isset(self::$stores[$name]);
	}

	public static function add(string $name, array $config): void
	{
		$store = self::build($name, $config);

		if ($store instanceof CacheInterface) {
			self::$stores[$name] = $store;
		}
	}

	public static function get(string|null $name = null): CacheInterface|null
	{
		if ($name === null) {
			$name = self::$default;
		}

		return self::$stores[$name] ?? null;
	}

	// -----------------

	/**
	 * @returns array<string, CacheInterface>
	 */
	public static function all(): array
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

}