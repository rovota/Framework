<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Length;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Length;

final class HectoMeter extends Length implements Metric
{

	const string SYMBOL = 'hm';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 100;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 100;
	}

}