<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use DateTime;
use DateTimeZone;
use Rovota\Framework\Kernel\RegistryManager;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Facade;

/**
 * @method static void import(array $entries)
 * @method static Bucket entries()
 *
 * @method static bool has(string $key)
 * @method static bool missing(string $key)
 * @method static mixed get(string $key, mixed $default = null)
 * @method static void set(string $key, mixed $value = null)
 * @method static void remove(string $key)
 *
 * @method static array array(string $key, array $default = [])
 * @method static bool bool(string $key, bool $default = false)
 * @method static float float(string $key, float $default = 0.00)
 * @method static int int(string $key, int $default = 0)
 * @method static string string(string $key, string $default = '')
 *
 * @method static DateTime|null date(string $key, DateTimeZone|null $timezone = null)
 */
final class Registry extends Facade
{

	public static function service(): RegistryManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return 'registry';
	}

	protected static function getMethodTarget(string $method): mixed
	{
		return $method;
	}

}