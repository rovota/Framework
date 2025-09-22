<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Speed;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Speed;

final class KiloMeterPerHour extends Speed implements Metric
{

	const string SYMBOL = 'km/h';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 3.6;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 3.6;
	}

}