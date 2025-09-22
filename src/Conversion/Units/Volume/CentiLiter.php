<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Volume;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Volume;

final class CentiLiter extends Volume implements Metric
{

	const string SYMBOL = 'cl';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1e+5;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1e+5;
	}

}