<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Pressure;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Pressure;

final class Pascal extends Pressure implements Metric
{

	const string SYMBOL = 'Pa';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1E5;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1E5;
	}

}