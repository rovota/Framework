<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Mass;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Traits\BaseUnit;
use Rovota\Framework\Conversion\Units\Mass;

final class Gram extends Mass implements Metric
{
	use BaseUnit;

	// -----------------

	const string SYMBOL = 'g';

}