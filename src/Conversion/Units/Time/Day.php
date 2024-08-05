<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Time;

use Rovota\Framework\Conversion\Units\Time;

final class Day extends Time
{

	const string SYMBOL = 'd';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 86400;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 86400;
	}

}