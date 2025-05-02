<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth\Drivers;

use Rovota\Framework\Auth\Adapters\StandardAdapter;
use Rovota\Framework\Auth\Provider;
use Rovota\Framework\Auth\ProviderConfig;

class Standard extends Provider
{

	public function __construct(string $name, ProviderConfig $config)
	{
		$adapter = new StandardAdapter($config->parameters);

		parent::__construct($name, $adapter, $config);
	}

}