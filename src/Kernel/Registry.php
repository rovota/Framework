<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Internal;

/**
 * @internal
 */
final class Registry
{

	protected static Bucket $entries;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 */
	public static function initialize(): void
	{
		$file = require Internal::projectFile('config/registry.php');

		self::$entries = new Bucket($file);
	}

	// -----------------

	public static function import(array $entries): void
	{
		self::$entries->import($entries);
	}

	public static function entries(): Bucket
	{
		return self::$entries;
	}

	// -----------------

	public static function has(mixed $key): bool
	{
		return self::$entries->has($key);
	}

	public static function missing(mixed $key): bool
	{
		return self::$entries->missing($key);
	}

	public static function get(mixed $key, mixed $default = null): mixed
	{
		return self::$entries->get($key, $default);
	}

	public static function remove(mixed $key): void
	{
		self::$entries->remove($key);
	}

	public static function set(mixed $key, mixed $value = null): void
	{
		self::$entries->set($key, $value);
	}

}