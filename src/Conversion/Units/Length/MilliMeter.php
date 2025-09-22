<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Length;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Length;

final class MilliMeter extends Length implements Metric
{

	const string SYMBOL = 'mm';

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