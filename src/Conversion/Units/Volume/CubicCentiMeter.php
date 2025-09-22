<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Volume;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Volume;

final class CubicCentiMeter extends Volume implements Metric
{

	const string SYMBOL = 'cm3';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1e+6;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1e+6;
	}

}