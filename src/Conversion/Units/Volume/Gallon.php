<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Volume;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\Volume;

final class Gallon extends Volume implements Imperial
{

	const string SYMBOL = 'gal';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 219.969;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 219.969;
	}

}