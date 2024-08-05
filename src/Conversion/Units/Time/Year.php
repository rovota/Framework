<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Time;

use Rovota\Framework\Conversion\Units\Time;

final class Year extends Time
{

	const string SYMBOL = 'y';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 31556952;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 31556952;
	}

}