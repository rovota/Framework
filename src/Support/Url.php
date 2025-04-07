<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Rovota\Framework\Caching\CacheManager;
use Rovota\Framework\Caching\Enums\Driver;
use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Routing\RouteManager;
use Rovota\Framework\Routing\UrlObject;
use Stringable;

final class Url
{

	protected function __construct()
	{
	}

	// -----------------

	public static function current(): UrlObject
	{
		return RequestManager::instance()->current()->url();
	}

	// -----------------

	public static function local(string $path, array $parameters = []): UrlObject
	{
		return new UrlObject([
			'path' => $path,
			'parameters' => $parameters,
		]);
	}

	public static function foreign(string $location, array $parameters = []): UrlObject
	{
		return UrlObject::from($location)->withParameters($parameters);
	}

	// -----------------

	public static function route(string $name, array $context = [], array $parameters = []): UrlObject
	{
		$route = RouteManager::instance()->router->findRouteByName($name);

		if ($route === null) {
			return self::local('/');
		}
		
		$path = Path::buildUsingContext($route->config->path, $context);

		return new UrlObject([
			'path' => $path,
			'parameters' => $parameters,
		]);
	}

	/**
	 * This method requires the presence of a cache store using the `session` driver.
	 */
	public static function previous(string $default = '/'): UrlObject
	{
		$referrer = RequestManager::instance()->current()->referrer();
		$location = CacheManager::instance()->getWithDriver(Driver::Session)?->pull('location.previous') ?? $referrer ?? $default;
		return UrlObject::from($location);
	}

	/**
	 * This method requires the presence of a cache store using the `session` driver.
	 */
	public static function next(string $default = '/'): UrlObject
	{
		$location = CacheManager::instance()->getWithDriver(Driver::Session)?->pull('location.next') ?? $default;
		return UrlObject::from($location);
	}

	/**
	 * This method requires the presence of a cache store using the `session` driver.
	 */
	public static function intended(string $default = '/'): UrlObject
	{
		$location = CacheManager::instance()->getWithDriver(Driver::Session)?->pull('location.intended') ?? $default;
		return UrlObject::from($location);
	}

	// -----------------

	public static function queryToArray(string $value): array
	{
		parse_str($value, $parameters);
		return $parameters;
	}

	public static function arrayToQuery(array $data): string
	{
		foreach ($data as $key => $value) {
			if ($value instanceof Stringable) {
				$data[$key] = (string) $value;
			}
		}
		return http_build_query($data);
	}

}