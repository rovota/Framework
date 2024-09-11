<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Traits;

use Closure;

trait Macroable
{

	/**
	 * @var array<string, Closure>
	 */
	protected static array $macros = [];

	// -----------------

	public static function macro(string $name, Closure $macro): void
	{
		self::$macros[$name] = $macro;
	}

	// -----------------

	public static function hasMacro(string $name): bool
	{
		return isset(static::$macros[$name]);
	}

}