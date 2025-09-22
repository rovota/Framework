<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Length;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Length;

final class MicroMeter extends Length implements Metric
{

	const string SYMBOL = 'μm';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1E6;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1E6;
	}

}