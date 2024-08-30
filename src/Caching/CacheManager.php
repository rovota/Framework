<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching;

use Rovota\Framework\Caching\Drivers\APCuDriver;
use Rovota\Framework\Caching\Drivers\ArrayDriver;
use Rovota\Framework\Caching\Drivers\RedisDriver;
use Rovota\Framework\Caching\Drivers\SessionDriver;
use Rovota\Framework\Caching\Enums\Driver;
use Rovota\Framework\Caching\Exceptions\CacheMisconfigurationException;
use Rovota\Framework\Caching\Exceptions\MissingCacheStoreException;
use Rovota\Framework\Caching\Interfaces\CacheInterface;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Path;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class CacheManager extends ServiceProvider
{

	/**
	 * @var Map<string, CacheInterface>
	 */
	protected Map $stores;

	protected string $default;

	// -----------------

	public function __construct()
	{
		$this->stores = new Map();

		$file = require Path::toProjectFile('config/caching.php');

		foreach ($file['stores'] as $name => $config) {
			$store = $this->build($name, $config);
			if ($store instanceof CacheInterface) {
				$this->stores->set($name, $store);
			}
		}

		$this->setDefault($file['default']);
	}

	// -----------------

	public function createStore(array $config, string|null $name = null): CacheInterface|null
	{
		return $this->build($name ?? Str::random(20), $config);
	}

	// -----------------

	public function has(string $name): bool
	{
		return isset($this->stores[$name]);
	}

	public function add(string $name, array $config): void
	{
		$store = $this->build($name, $config);

		if ($store instanceof CacheInterface) {
			$this->stores[$name] = $store;
		}
	}

	public function get(string|null $name = null): CacheInterface
	{
		if ($name === null) {
			$name = $this->default;
		}

		if (isset($this->stores[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingCacheStoreException("The specified cache could not be found: '$name'."));
		}

		return $this->stores[$name];
	}

	public function getWithDriver(Driver $driver): CacheInterface|null
	{
		return $this->stores->first(function (CacheInterface $store) use ($driver) {
			return $store->getConfig()->driver === $driver;
		});
	}

	// -----------------

	/**
	 * @returns Map<string, CacheInterface>
	 */
	public function all(): Map
	{
		return $this->stores;
	}

	// -----------------

	public function setDefault(string $name): void
	{
		if (isset($this->stores[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingCacheStoreException("Undefined caches cannot be set as default: '$name'."));
		}
		$this->default = $name;
	}

	public function getDefault(): string
	{
		return $this->default;
	}

	// -----------------

	protected function build(string $name, array $config): CacheInterface|null
	{
		$config = new CacheStoreConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException($config->get('driver')));
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
			Driver::Session => new SessionDriver($name, $config),
			default => null,
		};
	}

}