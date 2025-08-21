<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units;

use Rovota\Framework\Conversion\Enums\UnitType;
use Rovota\Framework\Conversion\Units\Temperature\Celsius;
use Rovota\Framework\Conversion\Units\Temperature\Fahrenheit;
use Rovota\Framework\Conversion\Units\Temperature\Kelvin;

/**
 * @method static self fromKelvin(float|int $value)
 * @method static self fromFahrenheit(float|int $value)
 * @method static self fromCelsius(float|int $value)
 *
 * @method self toKelvin()
 * @method self toFahrenheit()
 * @method self toCelsius()
 */
abstract class Temperature extends Unit
{
	const UnitType UNIT_TYPE = UnitType::Temperature;

	const string BASE_UNIT = Kelvin::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match ($identifier) {
			'K', 'Kelvin' => Kelvin::class,
			'°F', 'F', 'fahrenheit' => Fahrenheit::class,
			'°C', 'C', 'celsius' => Celsius::class,
			default => null,
		};
	}

}