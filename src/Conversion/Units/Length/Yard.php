<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Length;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\Length;

final class Yard extends Length implements Imperial
{

	const string SYMBOL = 'yd';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1.09361;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1.09361;
	}

}