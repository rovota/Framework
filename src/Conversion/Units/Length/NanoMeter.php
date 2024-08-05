<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Length;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Length;

final class NanoMeter extends Length implements Metric
{

	const string SYMBOL = 'nm';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1E9;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1E9;
	}

}