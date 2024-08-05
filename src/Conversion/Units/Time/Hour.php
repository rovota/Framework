<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Time;

use Rovota\Framework\Conversion\Units\Time;

final class Hour extends Time
{

	const string SYMBOL = 'h';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 3600;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 3600;
	}

}