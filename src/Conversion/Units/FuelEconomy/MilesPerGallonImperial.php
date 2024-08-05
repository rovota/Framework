<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\FuelEconomy;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\FuelEconomy;

final class MilesPerGallonImperial extends FuelEconomy implements Imperial
{

	const string SYMBOL = 'mpg';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 2.82481;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 2.82481;
	}

}