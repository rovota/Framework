<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Length;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\Length;

final class Mile extends Length implements Imperial
{

	const string SYMBOL = 'mi';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 1609.34;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 1609.34;
	}

}