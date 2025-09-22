<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Time;

use Rovota\Framework\Conversion\Units\Time;

final class Minute extends Time
{

	const string SYMBOL = 'm';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 60;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 60;
	}

}