<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\FuelEconomy;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Traits\BaseUnit;
use Rovota\Framework\Conversion\Units\FuelEconomy;

final class KiloMeterPerLiter extends FuelEconomy implements Metric
{
	use BaseUnit;

	// -----------------

	const string SYMBOL = 'km/l';

}