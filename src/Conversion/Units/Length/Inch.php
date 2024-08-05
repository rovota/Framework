<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Length;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\Length;

final class Inch extends Length implements Imperial
{

	const string SYMBOL = 'ft';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 39.3701;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 39.3701;
	}

}