<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Length;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Traits\BaseUnit;
use Rovota\Framework\Conversion\Units\Length;

final class Meter extends Length implements Metric
{
	use BaseUnit;

	// -----------------

	const string SYMBOL = 'm';

}