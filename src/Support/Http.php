<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

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

	// -----------------

	/**
	 * Attempts to retrieve a usable device name from a given useragent string. If nothing can be found, `Unknown` will be returned.
	 */
	public static function getApproximateDeviceFromUserAgent(string $useragent): string
	{
		$useragent = Text::from($useragent)->remove([
			'; x64', '; Win64', '; WOW64', '; K', ' like Mac OS X', 'X11; '
		])->after('(')->before(')')->before('; rv');

		if ($useragent->contains('CrOS')) {
			return $useragent
				->after('CrOS ')
				->beforeLast(' ')
				->replace(['x86_64', 'armv7l', 'aarch64'], ['x86 64-bit', 'ARM 32-bit', 'ARM 64-bit'])
				->wrap('(', ')')
				->prepend('Chromebook ');
		}

		if ($useragent->contains('iPhone')) {
			return $useragent
				->after('iPhone OS ')
				->replace('_', '.')
				->wrap('(iOS ', ')')
				->prepend('iPhone ');
		}

		if ($useragent->contains('iPad')) {
			return $useragent
				->after('CPU OS ')
				->replace('_', '.')
				->wrap('(iPadOS ', ')')
				->prepend('iPad ');
		}

		if ($useragent->contains('Macintosh')) {
			return $useragent
				->after('OS X ')
				->replace('_', '.')
				->wrap('(MacOS ', ')')
				->prepend('Mac ');
		}

		if ($useragent->contains('Android')) {
			return $useragent->afterLast('; ');
		}

		if ($useragent->contains('Windows')) {
			return $useragent
				->replace(['NT 10.0', 'NT 6.3', 'NT 6.2'], ['10/11', '8.1', '8.0'])
				->before(';');
		}

		return 'Unknown';
	}

}