<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Routing\RouteGroup;
use Rovota\Framework\Routing\RouteInstance;
use Rovota\Framework\Routing\RouteManager;
use Rovota\Framework\Routing\Router;
use Rovota\Framework\Support\Facade;
use Rovota\Framework\Support\Str;

/**
 * @method static Router router()
 */
final class Route extends Facade
{

	public static function service(): RouteManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return RouteManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'router' => 'getRouter',
			default => function (RouteManager $instance, string $method, array $parameters = []) {
				return $instance->getRouter()->$method(...$parameters);
			},
		};
	}

	// -----------------

	public static function match(array|string $methods, string $path, mixed $target = null): RouteInstance
	{
		$router = RouteManager::instance()->getRouter();
		return $router->define($methods, $path, $target);
	}

	// -----------------

	public static function name(string $value): RouteGroup
	{
		return self::getRouter()->getGroup()->name($value);
	}

	public static function prefix(string $path): RouteGroup
	{
		return self::getRouter()->getGroup()->prefix($path);
	}

	public static function controller(string $class): RouteGroup
	{
		return self::getRouter()->getGroup()->controller($class);
	}

	// -----------------

	protected static function getRouter(): Router
	{
		return RouteManager::instance()->getRouter();
	}

}