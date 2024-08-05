<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units;

use Rovota\Framework\Conversion\Enums\UnitType;
use Rovota\Framework\Conversion\Units\FuelEconomy\KiloMeterPerLiter;
use Rovota\Framework\Conversion\Units\FuelEconomy\LiterPerHundredKiloMeters;
use Rovota\Framework\Conversion\Units\FuelEconomy\MilesPerGallon;
use Rovota\Framework\Conversion\Units\FuelEconomy\MilesPerGallonImperial;

/**
 * @method static self fromKilometerPerLiter(float|int $value)
 * @method static self fromLiterPerHundredKilometers(float|int $value)
 * @method static self fromMilesPerGallon(float|int $value)
 * @method static self fromMilesPerGallonImperial(float|int $value)
 *
 * @method self toKilometerPerLiter()
 * @method self toLiterPerHundredKilometers()
 * @method self toMilesPerGallon()
 * @method self toMilesPerGallonImperial()
 */
abstract class FuelEconomy extends Unit
{
	const UnitType UNIT_TYPE = UnitType::FuelEconomy;

	const string BASE_UNIT = KiloMeterPerLiter::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match($identifier) {
			'km/l', 'kilometer per liter', 'kilometerperliter' => KiloMeterPerLiter::class,
			'l/100km', 'liter per 100 kilometers', 'literperhundredkilometers' => LiterPerHundredKiloMeters::class,
			'mpg', 'miles per gallon', 'milespergallon' => MilesPerGallon::class,
			'mpg imperial', 'miles per gallon imperial', 'milespergallonimperial' => MilesPerGallonImperial::class,
			default => null,
		};
	}

}