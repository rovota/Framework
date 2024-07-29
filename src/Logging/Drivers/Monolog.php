<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging\Drivers;

use Rovota\Framework\Logging\Channel;
use Rovota\Framework\Logging\ChannelConfig;

final class Monolog extends Channel
{

	public function __construct(string $name, ChannelConfig $config)
	{
		$handler = new $config->handler(...$config->parameters);

		parent::__construct($name, $handler, $config);
	}

}