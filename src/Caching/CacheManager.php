<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching;

use Rovota\Framework\Caching\Drivers\APCu;
use Rovota\Framework\Caching\Drivers\Memory;
use Rovota\Framework\Caching\Drivers\Redis;
use Rovota\Framework\Caching\Drivers\Session;
use Rovota\Framework\Caching\Enums\Driver;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\MisconfiguredServiceException;
use Rovota\Framework\Kernel\Exceptions\MissingInstanceException;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class CacheManager extends ServiceProvider
{

	/**
	 * @var Map<string, CacheStore>
	 */
	protected Map $stores;

	public readonly string $default;

	// -----------------

	public function __construct()
	{
		$this->stores = new Map();

		$file = CacheConfig::load('config/caching');

		foreach ($file->stores as $name => $config) {
			$this->stores->set($name, $this->build($name, $config));
		}

		if (count($file->stores) > 0 && isset($this->stores[$file->default])) {
			$this->default = $this->stores[$file->default];
		}
	}

	// -----------------

	public function createStore(array $config, string|null $name = null): CacheStore
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
		$this->stores[$name] = $this->build($name, $config);
	}

	public function get(string|null $name = null): CacheStore
	{
		if ($name === null && property_exists($this, 'default')) {
			$name = $this->default;
		}

		if (isset($this->stores[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingInstanceException("The specified cache could not be found: '$name'."));
		}

		return $this->stores[$name];
	}

	public function getWithDriver(Driver $driver): CacheStore|null
	{
		return $this->stores->first(function (CacheStore $store) use ($driver) {
			return $store->config->driver === $driver;
		});
	}

	// -----------------

	/**
	 * @returns Map<string, CacheStore>
	 */
	public function all(): Map
	{
		return $this->stores;
	}

	// -----------------

	protected function build(string $name, array $config): CacheStore
	{
		$config = new CacheStoreConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException($config->get('driver')));
			exit;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new MisconfiguredServiceException("The cache '$name' cannot be used due to a configuration issue."));
			exit;
		}

		return match ($config->driver) {
			Driver::APCu => new APCu($name, $config),
			Driver::Memory => new Memory($name, $config),
			Driver::Redis => new Redis($name, $config),
			Driver::Session => new Session($name, $config),
		};
	}

}