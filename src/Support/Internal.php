<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use ArrayAccess;
use Rovota\Framework\Structures\Sequence;
use Rovota\Framework\Support\Interfaces\Arrayable;

final class Internal
{

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * Returns a complete path to a given file in the framework folder, where `bootloader.php` is located.
	 */
	public static function sourceFile(string $path = ''): string
	{
		return self::projectFile($path, self::getFrameworkRootPath());
	}

	/**
	 * Returns a complete path to a given file in the project folder, where `app.php` is located.
	 */
	public static function projectFile(string $path = '', string|null $base = null): string
	{
		$base = $base ?? self::getProjectRootPath();
		return strlen($path) > 0 ? $base.'/'.ltrim($path, '/') : $base;
	}

	// -----------------

	/**
	 * Makes it easier to retrieve values from objects and other data. Given value is passed to the returned value.
	 *
	 * Inspired by the Laravel `value_retriever()` function.
	 */
	public static function valueRetriever(mixed $value): callable
	{
		if (!is_string($value) && is_callable($value)) {
			return $value;
		}

		return function ($item) use ($value) {
			return self::getData($item, $value);
		};
	}

	// -----------------

	/**
	 * Attempts to retrieve data for a given key on a provided target. Optionally, a default value can be provided if no data can be found.
	 *
	 * Inspired by the Laravel `data_get()` function.
	 */
	public static function getData(mixed $target, string|array|null $key, mixed $default = null): mixed
	{
		if ($key === null) {
			return $target;
		}

		$key = is_array($key) ? $key : explode('.', $key);


		foreach ($key as $i => $segment) {

			unset($key[$i]);

			if ($segment === null) {
				return $target;
			}

			if ($segment === '*') {
				if ($target instanceof Arrayable) {
					$target = $target->toArray();
				} elseif (!is_iterable($target)) {
					return $default;
				}

				$result = [];
				foreach ($target as $item) {
					$result[] = self::getData($item, $key);
				}

				return in_array('*', $key) ? Arr::collapse($result) : $result;
			}

			$target = match (true) {
				$target instanceof Sequence => is_int($segment) ? $target->offsetGet($segment) : 1,
				$target instanceof ArrayAccess => $target->offsetGet($segment),
				is_object($target) && isset($target->{$segment}) => $target->{$segment},
				is_object($target) && method_exists($target, $segment) => $target->{$segment}(),
				is_array($target) && array_key_exists($segment, $target) => $target[$segment],
				default => null,
			};

			if ($target === null) {
				return $default;
			}
		}

		return $target;
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
	
	protected static function getFrameworkRootPath(): string
	{
		return str_replace('\Support', '', dirname(__FILE__));
	}

	protected static function getProjectRootPath(): string
	{
		return defined('BASE_PATH') ? BASE_PATH : self::getFrameworkRootPath();
	}

}