<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Drivers;

use Rovota\Framework\Caching\Adapters\RedisAdapter;
use Rovota\Framework\Caching\CacheStore;
use Rovota\Framework\Caching\CacheStoreConfig;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\MisconfiguredServiceException;
use Throwable;

class Redis extends CacheStore
{

	public function __construct(string $name, CacheStoreConfig $config)
	{
		if (extension_loaded('redis')) {
			ExceptionHandler::handleThrowable(new MisconfiguredServiceException("The Redis extension is required in order to use this driver."));
			quit();
		}

		try {
			$adapter = new RedisAdapter($config->parameters);
		} catch (Throwable $throwable) {
			ExceptionHandler::handleThrowable($throwable);
			quit();
		}

		parent::__construct($name, $adapter, $config);
	}

}