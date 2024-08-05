<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Speed;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\Speed;

final class MilePerHour extends Speed implements Imperial
{

	const string SYMBOL = 'mp/h';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 2.23694;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 2.23694;
	}

}