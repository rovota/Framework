<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Frequency;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Traits\BaseUnit;
use Rovota\Framework\Conversion\Units\Frequency;

final class Hertz extends Frequency implements Metric
{
	use BaseUnit;

	// -----------------

	const string SYMBOL = 'hz';

}