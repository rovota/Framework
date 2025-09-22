<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Traits;

use Rovota\Framework\Conversion\Units\Unit;

trait BaseUnit
{

	protected function toBaseUnit(): Unit
	{
		if (static::class === self::class) {
			return $this;
		}
		return parent::toBaseUnit();
	}

}