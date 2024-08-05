<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Mass;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\Mass;

final class Ounce extends Mass implements Imperial
{

	const string SYMBOL = 'oz';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 28.3495;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 28.3495;
	}

}