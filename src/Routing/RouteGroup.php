<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use Closure;

final class RouteGroup extends RouteEntry
{

	public function controller(string $class): RouteGroup
	{
		$this->attributes->set('controller', $class);
		return $this;
	}

	// -----------------

	public function group(Closure $routes): void
	{
		RouteManager::instance()->router->group($routes, $this);
	}

}