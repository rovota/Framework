<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use Closure;

final class RouteGroup extends RouteEntry
{

	public function prefix(string $path): RouteGroup
	{
		$path = trim($path, '/');

		if ($this->attributes->has('prefix')) {
			$path = implode('/', [$this->attributes->get('prefix'), $path]);
		}

		$this->attributes->set('prefix', $path);
		return $this;
	}

	// -----------------

	public function controller(string $class): RouteGroup
	{
		$this->attributes->set('controller', $class);
		return $this;
	}

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	public function group(Closure $routes): void
	{
		$router = RouteManager::instance()->getRouter();
		$router->setParent($this);
		call_user_func($routes);
		$router->removeParent();
	}

}