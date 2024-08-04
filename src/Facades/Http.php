<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Rovota\Framework\Http\Client\Client;
use Rovota\Framework\Http\Client\Integrations\HibpClient;
use Rovota\Framework\Http\Client\Request;

final class Http
{

	protected function __construct()
	{
	}

	// -----------------

	public static function client(mixed $options = []): Client
	{
		return new Client($options);
	}

	public static function HibpClient(string|null $key = null, mixed $options = []): HibpClient
	{
		return new HibpClient($key, $options);
	}

	// -----------------

	public static function request(string $method, string $location, array $options = []): Request
	{
		return self::client($options)->request($method, $location);
	}

	public static function get(string $location, array $options = []): Request
	{
		return self::client($options)->get($location);
	}

	public static function delete(string $location, array $options = []): Request
	{
		return self::client($options)->delete($location);
	}

	public static function head(string $location, array $options = []): Request
	{
		return self::client($options)->head($location);
	}

	public static function options(string $location, array $options = []): Request
	{
		return self::client($options)->options($location);
	}

	public static function patch(string $location, array $options = []): Request
	{
		return self::client($options)->patch($location);
	}

	public static function post(string $location, array $options = []): Request
	{
		return self::client($options)->post($location);
	}

	public static function put(string $location, array $options = []): Request
	{
		return self::client($options)->put($location);
	}

}