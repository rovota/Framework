<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Volume;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Volume;

final class CubicDeciMeter extends Volume implements Metric
{

	const string SYMBOL = 'dm3';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1000;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1000;
	}

}