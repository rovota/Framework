<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Length;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Length;

final class DeciMeter extends Length implements Metric
{

	const string SYMBOL = 'dm';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 10;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 10;
	}

}