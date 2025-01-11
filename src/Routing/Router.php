<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * The parameter extraction logic has been derived from bramus/router:
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
use Rovota\Framework\Http\Request\RequestObject;
use Rovota\Framework\Http\Response\DefaultResponse;
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
	protected Bucket $routes;

	protected RouteInstance|null $current = null;

	protected RouteInstance|null $fallback = null;

	protected RouteGroup|null $parent = null;

	// -----------------

	public function __construct()
	{
		$this->routes = new Bucket();

		$this->setFallback(StatusCode::NotFound);
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

	public function setParent(RouteGroup $parent): void
	{
		$this->parent = $parent;
	}

	public function removeParent(): void
	{
		$this->parent = null;
	}

	// -----------------

	public function getGroup(): RouteGroup
	{
		return new RouteGroup($this->parent);
	}

	public function getRoutes(): Bucket
	{
		return $this->routes;
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

	public function define(array|string $methods, string $path, mixed $target = null): RouteInstance
	{
		$route = new RouteInstance($this->parent);
		$route->methods($methods);
		$route->target($target);
		$route->path($path);

		$this->routes->append($route);

		return $route;
	}

	// -----------------

	public function run(): void
	{
		$response = $this->attemptRoutes();

		if ($response === null) {
			$response = $this->executeRoute($this->fallback, []);
		}

		echo $response;

		if ($this->getRequest()->realMethod() === RequestMethod::Head) {
			Buffer::end();
		}
	}

	// -----------------

	protected function attemptRoutes(): DefaultResponse|null
	{
		$method = $this->getRequest()->method();
		$path = $this->getRequest()->path();

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
		$route->setContext($parameters);
//
//		if ($route->hasLimiter()) {
//			LimitManager::hitAndTryLimiter($route->getLimiter());
//		}
//
//		MiddlewareManager::execute($route->getMiddleware(), $route->getWithoutMiddleware());
		// TODO: Attach middleware added to routes.
		MiddlewareManager::instance()->execute([]);
		
		if ($route->getTarget() instanceof Closure || is_array($route->getTarget())) {
			$response = Resolver::invoke($route->getTarget(), $parameters);
		} else {
			$response = $route->getTarget();
		}

		return ($response instanceof DefaultResponse) ? $response : response($response);
	}

	// -----------------

	protected function getExtractedParameters(array $matches): array
	{
		// Rework matches to only contain the matches, not the original string
		$matches = array_slice($matches, 1);

		// Extract the matched URL parameters (and only the parameters)
		return array_map(function ($match, $index) use ($matches) {

			// We have a following parameter: take the substring from the current param position until the next one's position (thank you PREG_OFFSET_CAPTURE)
			if (isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
				if ($matches[$index + 1][0][1] > -1) {
					return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
				}
			} // We have no following parameters: return the lot

			return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], '/') : null;
		}, $matches, array_keys($matches));
	}

	// -----------------

	protected function getRequest(): RequestObject
	{
		return RequestManager::instance()->getCurrent();
	}

}