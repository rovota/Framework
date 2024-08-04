<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Text;

/**
 * @internal
 */
final class RequestManager
{
	protected static Request $current;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		self::$current = new Request([
			'headers' => getallheaders(),
			'body' => self::getRequestBody(),
			'post' => self::getRequestPostData(),
			'query' => self::getRequestQueryData(),
		]);
	}

	// -----------------

	public static function getCurrent(): Request
	{
		return self::$current;
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

	// -----------------

	protected static function getRequestBody(): string|null
	{
		$body = file_get_contents('php://input');
		return $body === false ? null : trim($body);
	}

	protected static function getRequestPostData(): array
	{
		$data = $_POST;
		array_walk_recursive($data, function(&$item) {
			if (is_string($item)) {
				$item = mb_strlen(trim($item)) > 0 ? trim($item) : null;
			}
		});

		// TODO: Implement request files processing.

//		$files = FilesArrayOrganizer::organize($_FILES);

//		return array_merge($data, $files);
		return $data;
	}

	protected static function getRequestQueryData(): array
	{
		$url = Framework::environment()->server()->get('REQUEST_URI');

		if (Str::contains($url, '?')) {
			parse_str(Str::after($url, '?'), $parameters);

			return Arr::map($parameters, function ($value) {
				return $value === null ? null : trim($value);
			});
		}

		return [];
	}

}