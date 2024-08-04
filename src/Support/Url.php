<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Rovota\Framework\Http\RequestManager;
use Rovota\Framework\Routing\UrlObject;

final class Url
{

	protected function __construct()
	{
	}

	// -----------------

	public static function current(): UrlObject
	{
		return RequestManager::getCurrent()->url();
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

	// TODO: route()

	// TODO: previous()

	// TODO: next()

	// TODO: intended()

	// -----------------

	public static function queryToArray(string $value): array
	{
		parse_str($value, $parameters);
		return $parameters;
	}

	public static function arrayToQuery(array $data): string
	{
		return http_build_query($data);
	}

}