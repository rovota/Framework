<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Pressure;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Traits\BaseUnit;
use Rovota\Framework\Conversion\Units\Pressure;

final class Bar extends Pressure implements Metric
{
	use BaseUnit;

	// -----------------

	const string SYMBOL = 'bar';

}