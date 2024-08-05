<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Temperature;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\Temperature;

final class Fahrenheit extends Temperature implements Imperial
{

	const string SYMBOL = '°F';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return ($value - 32) * (5/9) + 273.15;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return ($value - 273.15) * (9/5) + 32;
	}

}