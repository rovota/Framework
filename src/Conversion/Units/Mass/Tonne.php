<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Mass;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Mass;

final class Tonne extends Mass implements Metric
{

	const string SYMBOL = 'tonne';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 1E6;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 1E6;
	}

}