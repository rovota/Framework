<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Time;

use Rovota\Framework\Conversion\Units\Time;

final class MicroSecond extends Time
{

	const string SYMBOL = 'μs';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1E6;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1E6;
	}

}