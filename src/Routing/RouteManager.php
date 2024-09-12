<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use Rovota\Framework\Kernel\ServiceProvider;

/**
 * @internal
 */
final class RouteManager extends ServiceProvider
{

	protected Router $router;

	// -----------------

	public function __construct()
	{
		$this->router = new Router();
	}

	// -----------------

	public function getRouter(): Router
	{
		return $this->router;
	}

}