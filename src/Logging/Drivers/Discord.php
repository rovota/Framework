<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging\Drivers;

use Rovota\Framework\Logging\Channel;
use Rovota\Framework\Logging\ChannelConfig;
use Rovota\Framework\Logging\Handlers\DiscordHandler;

final class Discord extends Channel
{

	public function __construct(string $name, ChannelConfig $config)
	{
		$handler = new DiscordHandler(...$config->parameters);

		parent::__construct($name, $handler, $config);
	}

}