<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging\Drivers;

use Monolog\Handler\StreamHandler;
use Rovota\Framework\Logging\Channel;
use Rovota\Framework\Logging\ChannelConfig;

final class Stream extends Channel
{

	public function __construct(string $name, ChannelConfig $config)
	{
		$handler = new StreamHandler(...$config->parameters);

		parent::__construct($name, $handler, $config);
	}

}