<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units;

use Rovota\Framework\Conversion\Enums\UnitType;
use Rovota\Framework\Conversion\Units\Pressure\Bar;
use Rovota\Framework\Conversion\Units\Pressure\Pascal;
use Rovota\Framework\Conversion\Units\Pressure\PoundPerSquareInch;

/**
 * @method static self fromBar(float|int $value)
 * @method static self fromPascal(float|int $value)
 * @method static self fromPsi(float|int $value)
 *
 * @method self toBar()
 * @method self toPascal()
 * @method self toPsi()
 */
abstract class Pressure extends Unit
{
	const UnitType UNIT_TYPE = UnitType::Pressure;

	const string BASE_UNIT = Bar::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match($identifier) {
			'bar' => Bar::class,
			'pascal', 'pa' => Pascal::class,
			'psi', 'lbf/in2', 'pound per square inch' => PoundPerSquareInch::class,
			default => null,
		};
	}

}