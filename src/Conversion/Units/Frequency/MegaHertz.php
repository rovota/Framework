<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Frequency;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Frequency;

final class MegaHertz extends Frequency implements Metric
{

	const string SYMBOL = 'mhz';

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