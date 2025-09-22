<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Volume;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\Volume;

final class CubicFoot extends Volume implements Imperial
{

	const string SYMBOL = 'ft3';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 35.3147;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 35.3147;
	}

}