<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\FuelEconomy;

use Rovota\Framework\Conversion\Interfaces\USC;
use Rovota\Framework\Conversion\Units\FuelEconomy;

final class MilesPerGallon extends FuelEconomy implements USC
{

	const string SYMBOL = 'mpg';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 2.35215;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 2.35215;
	}

}