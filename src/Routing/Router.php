<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * @author      Bram(us) Van Damme <bramus@bram.us>
 * @copyright   Copyright (c), 2013 Bram(us) Van Damme
 * @license     MIT public license
 */

namespace Rovota\Framework\Routing;

use Closure;
use Rovota\Framework\Http\Enums\RequestMethod;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\MiddlewareManager;
use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Http\Response\DefaultResponse;
use Rovota\Framework\Http\Throttling\LimitManager;
use Rovota\Framework\Kernel\Resolver;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Buffer;

/**
 * @internal
 */
final class Router
{

	const array ACCEPTED_METHODS = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH', 'HEAD'];

	// -----------------

	/**
	 * @var Bucket<int, RouteInstance>
	 */
	protected Bucket $routes {
		get {
			return $this->routes;
		}
	}

	protected RouteGroup|null $parent = null;

	// -----------------

	protected RouteInstance|null $current = null;

	protected RouteInstance|null $fallback = null;

	// -----------------

	public function __construct()
	{
		$this->routes = new Bucket();

		$this->setFallback(StatusCode::NotFound);
	}

	// -----------------

	public function define(array|string $methods, string $path, mixed $target = null): RouteInstance
	{
		$route = new RouteInstance($this->parent);
		$route->methods($methods);
		$route->target($target);
		$route->path($path);

		$this->routes->append($route);

		return $route;
	}

	public function generate(string $controller): RouteGenerator
	{
		return new RouteGenerator($controller);
	}

	public function group(Closure $routes, RouteGroup $parent): void
	{
		$original_parent = $this->parent;
		$this->parent = $parent;

		call_user_func($routes);

		$this->parent = $original_parent;
	}

	// -----------------

	public function setFallback(mixed $target = null): RouteInstance
	{
		if ($target !== null) {
			$route = new RouteInstance();
			$route->target($target);
			$this->fallback = $route;
		}

		return $this->fallback;
	}

	// -----------------

	public function getGroup(): RouteGroup
	{
		return new RouteGroup($this->parent);
	}

	public function getCurrentRoute(): RouteInstance|null
	{
		return $this->current;
	}

	public function findRouteByName(string $name): RouteInstance|null
	{
		return $this->routes->first(function (RouteInstance $route) use ($name) {
			return $route->getName() === $name;
		});
	}

	public function findRoutesWithGroupName(string $name): Bucket
	{
		return $this->routes->filter(function (RouteInstance $route) use ($name) {
			return str_starts_with($route->getName(), $name) && text($route->getName())->remove($name)->startsWith('.');
		});
	}

	// -----------------

	public function run(): void
	{
		$response = $this->attemptRoutes();

		if ($response === null) {
			$response = $this->executeRoute($this->fallback, []);
		}

		echo $response;

		if (RequestManager::instance()->current()->realMethod() === RequestMethod::Head) {
			Buffer::end();
		}
	}

	// -----------------

	protected function attemptRoutes(): DefaultResponse|null
	{
		$method = RequestManager::instance()->current()->method();
		$path = RequestManager::instance()->current()->path();

		/** @var RouteInstance $route */
		foreach ($this->routes as $route) {
			if ($route->listensTo($method)) {
				if (preg_match_all('#^' . $route->getPattern() . '$#', $path, $matches, PREG_OFFSET_CAPTURE) === 1) {
					$parameters = $this->getExtractedParameters($matches);
					return $this->executeRoute($route, $parameters);
				}
			}
		}

		return null;
	}

	protected function executeRoute(RouteInstance $route, array $parameters): DefaultResponse|null
	{
		$this->current = $route;
		$route->config->context = $parameters;

		$this->triggerLimiterForRoute($route);
		$this->triggerMiddlewareForRoute($route);
		
		if ($route->config->target instanceof Closure || is_array($route->config->target)) {
			$response = Resolver::invoke($route->config->target, $parameters);
		} else {
			$response = $route->config->target;
		}

		return ($response instanceof DefaultResponse) ? $response : response($response);
	}

	// -----------------

	protected function triggerLimiterForRoute(RouteInstance $route): void
	{
		if ($route->attributes->has('limiter')) {
			LimitManager::instance()->get($route->attributes->string('limiter'))->hitAndTry();
		}
	}

	protected function triggerMiddlewareForRoute(RouteInstance $route): void
	{
		$middleware = $route->attributes->array('middleware');
		$middleware_exceptions = $route->attributes->array('middleware_exceptions');

		MiddlewareManager::instance()->execute($middleware, $middleware_exceptions);
	}

	// -----------------

	protected function getExtractedParameters(array $matches): array
	{
		// Rework matches to only contain the matches, not the original string
		$matches = array_slice($matches, 1);

		// Extract the matched URL parameters
		return array_map(function ($match, $index) use ($matches) {

			// We have the following parameter: take the substring from the current param position until the next position
			if (isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
				if ($matches[$index + 1][0][1] > -1) {
					return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
				}
			} // We have no further parameters: return the lot

			return isset($match[0][0]) && $match[0][1] != -1 ? urldecode(trim($match[0][0], '/')) : null;
		}, $matches, array_keys($matches));
	}

}