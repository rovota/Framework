<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units\Mass;

use Rovota\Framework\Conversion\Interfaces\Imperial;
use Rovota\Framework\Conversion\Units\Mass;

final class Pound extends Mass implements Imperial
{

	const string SYMBOL = 'lb';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 453.592;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 453.592;
	}

}