<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Drivers;

use Rovota\Framework\Caching\Adapters\SessionAdapter;
use Rovota\Framework\Caching\CacheStore;
use Rovota\Framework\Caching\CacheStoreConfig;

class Session extends CacheStore
{

	public function __construct(string $name, CacheStoreConfig $config)
	{
		$adapter = new SessionAdapter($config->parameters);

		parent::__construct($name, $adapter, $config);
	}

}