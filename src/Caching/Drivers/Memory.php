<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Drivers;

use Rovota\Framework\Caching\Adapters\ArrayAdapter;
use Rovota\Framework\Caching\CacheStore;
use Rovota\Framework\Caching\CacheStoreConfig;

class Memory extends CacheStore
{

	public function __construct(string $name, CacheStoreConfig $config)
	{
		$adapter = new ArrayAdapter($config->parameters);

		parent::__construct($name, $adapter, $config);
	}

}