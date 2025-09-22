<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Speed;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\Speed;

final class FootPerSecond extends Speed implements Imperial
{

	const string SYMBOL = 'ft/s';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 3.28084;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 3.28084;
	}

}