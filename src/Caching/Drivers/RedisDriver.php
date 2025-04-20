<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Drivers;

use Rovota\Framework\Caching\Adapters\RedisAdapter;
use Rovota\Framework\Caching\CacheStore;
use Rovota\Framework\Caching\CacheStoreConfig;
use Rovota\Framework\Caching\Exceptions\CacheMisconfigurationException;
use Rovota\Framework\Kernel\ExceptionHandler;
use Throwable;

class RedisDriver extends CacheStore
{

	public function __construct(string $name, CacheStoreConfig $config)
	{
		if (extension_loaded('redis')) {
			ExceptionHandler::handleThrowable(new CacheMisconfigurationException("The Redis extension is required in order to use this driver."));
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