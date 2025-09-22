<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Time;

use Rovota\Framework\Conversion\Units\Time;

final class MilliSecond extends Time
{

	const string SYMBOL = 'ms';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1E3;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1E3;
	}

}