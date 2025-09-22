<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Speed;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Traits\BaseUnit;
use Rovota\Framework\Conversion\Units\Speed;

final class MeterPerSecond extends Speed implements Metric
{
	use BaseUnit;

	// -----------------

	const string SYMBOL = 'm/s';

}