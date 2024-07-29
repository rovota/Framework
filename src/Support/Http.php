<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

final class Http
{

	protected function __construct()
	{
	}

	// -----------------

	public static function acceptHeaderToArray(string|null $header): array
	{
		$header = trim($header ?? '');
		if (mb_strlen($header) === 0) {
			return [];
		}
		return array_reduce(explode(',', $header),
			function ($carry, $element) {
				$type = Str::before($element, ';');
				$quality = str_contains($element, ';q=') ? Str::afterLast($element, ';q=') : 1.00;
				$carry[trim($type)] = (float) $quality;
				return $carry;
			},[]
		);
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