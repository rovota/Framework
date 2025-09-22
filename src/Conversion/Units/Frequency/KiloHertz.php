<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Frequency;

use Rovota\Framework\Conversion\Interfaces\Metric;
use Rovota\Framework\Conversion\Units\Frequency;

final class KiloHertz extends Frequency implements Metric
{

	const string SYMBOL = 'khz';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 1000;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 1000;
	}

}