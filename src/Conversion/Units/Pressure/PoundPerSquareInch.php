<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Pressure;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Pressure;

final class PoundPerSquareInch extends Pressure implements Metric
{

	const string SYMBOL = 'psi';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 14.5038;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 14.5038;
	}

}