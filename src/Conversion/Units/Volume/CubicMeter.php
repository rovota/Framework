<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Volume;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Traits\BaseUnit;
use Rovota\Framework\Conversion\Units\Volume;

final class CubicMeter extends Volume implements Metric
{
	use BaseUnit;

	// -----------------

	const string SYMBOL = 'm3';

}