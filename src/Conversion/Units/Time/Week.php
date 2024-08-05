<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Time;

use Rovota\Framework\Conversion\Units\Time;

final class Week extends Time
{

	const string SYMBOL = 'w';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 604800;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 604800;
	}

}