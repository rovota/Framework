<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Frequency;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Frequency;

final class GigaHertz extends Frequency implements Metric
{

	const string SYMBOL = 'ghz';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 1E9;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 1E9;
	}

}