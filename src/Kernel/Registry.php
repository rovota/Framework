<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use BackedEnum;
use DateTime;
use DateTimeZone;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Internal;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Support\Text;

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

	// -----------------

	public static function array(string $key, array $default = []): array
	{
		return self::$entries->array($key, $default);
	}

	public static function bool(string $key, bool $default = false): bool
	{
		return self::$entries->bool($key, $default);
	}

	public static function date(string $key, DateTimeZone|null $timezone = null): DateTime|null
	{
		return self::$entries->date($key, $timezone);
	}

	public static function enum(string $key, BackedEnum|string $class, BackedEnum|null $default = null): BackedEnum|null
	{
		return self::$entries->enum($key, $class, $default);
	}

	public static function float(string $key, float $default = 0.00): float|false
	{
		return self::$entries->float($key, $default);
	}

	public static function int(string $key, int $default = 0): int|false
	{
		return self::$entries->int($key, $default);
	}

	public static function string(string $key, string $default = ''): string
	{
		return self::$entries->string($key, $default);
	}

	// -----------------

	public static function text(string $key, Text $default = new Text()): Text
	{
		return self::$entries->text($key, $default);
	}

	public static function moment(string $key, mixed $default = null, DateTimeZone|int|string|null $timezone = null): Moment|null
	{
		return self::$entries->moment($key, $default, $timezone);
	}

}