<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use Rovota\Framework\Support\Config;

class RouteGeneratorConfig extends Config
{

	public string $controller {
		get => $this->string('controller');
	}

	public array $actions {
		get => $this->array('actions');
	}

}