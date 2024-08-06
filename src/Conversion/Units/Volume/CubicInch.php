<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Volume;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\Volume;

final class CubicInch extends Volume implements Imperial
{

	const string SYMBOL = 'in3';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 61023.7;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 61023.7;
	}

}