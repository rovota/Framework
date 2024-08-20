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
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Internal;
use Rovota\Framework\Support\Str;

final class CacheManager extends ServiceProvider
{

	/**
	 * @var Map<string, CacheInterface>
	 */
	protected Map $stores;

	protected string $default;

	// -----------------

	/**
	 * @internal
	 */
	public function __construct()
	{
		$this->stores = new Map();

		$config = require Internal::projectFile('config/caching.php');

		foreach ($config['stores'] as $name => $options) {
			$store =  $this->build($name, $options);
			if ($store instanceof CacheInterface) {
				$this->stores->set($name, $store);
			}
		}

		$this->setDefault($config['default']);
	}

	// -----------------

	public function createStore(array $options, string|null $name = null): CacheInterface|null
	{
		return $this->build($name ?? Str::random(20), $options);
	}

	// -----------------

	public function hasStore(string $name): bool
	{
		return isset($this->stores[$name]);
	}

	public function addStore(string $name, array $config): void
	{
		$store = $this->build($name, $config);

		if ($store instanceof CacheInterface) {
			$this->stores[$name] = $store;
		}
	}

	public function getStore(string|null $name = null): CacheInterface
	{
		if ($name === null) {
			$name = $this->default;
		}

		if (isset($this->stores[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingCacheStoreException("The specified cache could not be found: '$name'."));
		}

		return $this->stores[$name];
	}

	// -----------------

	/**
	 * @returns Map<string, CacheInterface>
	 */
	public function getStores(): Map
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