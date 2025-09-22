<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Length;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\Length;

final class NauticalMile extends Length implements Imperial
{

	const string SYMBOL = 'NM';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 0.000539957;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 0.000539957;
	}

}