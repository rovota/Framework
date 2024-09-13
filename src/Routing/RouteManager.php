<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Support\Path;

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

	public function importRoutes(): void
	{
		$file = require Path::toProjectFile('config/routing.php');

		if (isset($file['fallback'])) {
			$this->router->setFallback($file['fallback']);
		}

		foreach ($file['sources'] as $source) {
			require $source;
		}
	}

	// -----------------

	public function getRouter(): Router
	{
		return $this->router;
	}

	public function getRouteGroup(): RouteGroup
	{
		return $this->router->getGroup();
	}

}