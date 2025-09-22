<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\FuelEconomy;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\FuelEconomy;

final class LiterPerHundredKiloMeters extends FuelEconomy implements Metric
{

	const string SYMBOL = 'l/100km';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return 100 / $value;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return 100 / $value;
	}

}