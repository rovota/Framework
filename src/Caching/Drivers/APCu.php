<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Drivers;

use Rovota\Framework\Caching\Adapters\APCuAdapter;
use Rovota\Framework\Caching\CacheStore;
use Rovota\Framework\Caching\CacheStoreConfig;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\MisconfiguredServiceException;

class APCu extends CacheStore
{

	public function __construct(string $name, CacheStoreConfig $config)
	{
		if (extension_loaded('apcu') === false) {
			ExceptionHandler::handleThrowable(new MisconfiguredServiceException("The APCu extension is required in order to use this driver."));
			quit();
		}

		$adapter = new APCuAdapter($config->parameters);

		parent::__construct($name, $adapter, $config);
	}

}