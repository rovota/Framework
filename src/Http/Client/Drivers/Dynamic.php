<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client\Drivers;

use Rovota\Framework\Http\Client\Client;
use Rovota\Framework\Http\Client\ClientConfig;
use Rovota\Framework\Http\Client\Connectors\DynamicConnector;

class Dynamic extends Client
{

	public function __construct(string $name, ClientConfig $config)
	{
		$connector = new DynamicConnector($config->url, $config->options, $config->timeouts);

		parent::__construct($name, $connector, $config);
	}

}