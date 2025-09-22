<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Temperature;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Temperature;

final class Celsius extends Temperature implements Metric
{

	const string SYMBOL = '°C';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value + 273.15;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value - 273.15;
	}

}