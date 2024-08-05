<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Speed;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\Speed;

final class Knot extends Speed implements Imperial
{

	const string SYMBOL = 'kn';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1.94384;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1.94384;
	}

}