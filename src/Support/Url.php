<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Rovota\Framework\Routing\UrlObject;

final class Url
{

	protected function __construct()
	{
	}

	// -----------------

	public static function local(string $location, array $parameters = []): UrlObject
	{
		return new UrlObject([
			'path' => $location,
			'parameters' => $parameters,
		]);
	}

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

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