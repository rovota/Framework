<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Mass;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Mass;

final class MilliGram extends Mass implements Metric
{

	const string SYMBOL = 'mg';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1E3;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1E3;
	}

}