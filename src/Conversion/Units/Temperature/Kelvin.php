<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Temperature;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Traits\BaseUnit;
use Rovota\Framework\Conversion\Units\Temperature;

final class Kelvin extends Temperature implements Metric
{
	use BaseUnit;

	// -----------------

	const string SYMBOL = 'K';

}