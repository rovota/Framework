<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Rovota\Framework\Kernel\Application;

final class Math
{

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * Determines whether two floating values are equal, accounting for rounding precision.
	 */
	public static function floatEquals(float $first, float $second, int $precision = Application::DEFAULT_FLOAT_PRECISION): bool
	{
		return round($first, $precision) === round($second, $precision);
	}

}