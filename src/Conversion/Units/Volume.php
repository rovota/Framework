<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units;

use Rovota\Framework\Conversion\Enums\UnitType;
use Rovota\Framework\Conversion\Units\Volume\CentiLiter;
use Rovota\Framework\Conversion\Units\Volume\CubicCentiMeter;
use Rovota\Framework\Conversion\Units\Volume\CubicDeciMeter;
use Rovota\Framework\Conversion\Units\Volume\CubicFoot;
use Rovota\Framework\Conversion\Units\Volume\CubicInch;
use Rovota\Framework\Conversion\Units\Volume\CubicMeter;
use Rovota\Framework\Conversion\Units\Volume\DeciLiter;
use Rovota\Framework\Conversion\Units\Volume\Gallon;
use Rovota\Framework\Conversion\Units\Volume\Liter;
use Rovota\Framework\Conversion\Units\Volume\MilliLiter;

/**
 * @method static self fromCubicMeters(float|int $value)
 * @method static self fromCubicCentimeters(float|int $value)
 * @method static self fromCubicDecimeters(float|int $value)
 * @method static self fromMilliliters(float|int $value)
 * @method static self fromCentiliters(float|int $value)
 * @method static self fromDeciliters(float|int $value)
 * @method static self fromLiters(float|int $value)
 * @method static self fromCubicFeet(float|int $value)
 * @method static self fromCubicInches(float|int $value)
 * @method static self fromGallons(float|int $value)
 *
 * @method self toCubicMeters()
 * @method self toCubicCentimeters()
 * @method self toCubicDecimeters()
 * @method self toMilliliters()
 * @method self toCentiliters()
 * @method self toDeciliters()
 * @method self toLiters()
 * @method self toCubicFeet()
 * @method self toCubicInches()
 * @method self toGallons()
 */
abstract class Volume extends Unit
{
	const UnitType UNIT_TYPE = UnitType::Volume;

	const string BASE_UNIT = CubicMeter::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match($identifier) {
			'm3', 'cubicmeters' => CubicMeter::class,
			'cm3', 'cubiccentimeters' => CubicCentiMeter::class,
			'dm3', 'cubicdecimeters' => CubicDeciMeter::class,
			'ml', 'milliliters' => MilliLiter::class,
			'cl', 'centiliters' => CentiLiter::class,
			'dl', 'deciliters' => DeciLiter::class,
			'l', 'liters' => Liter::class,

			'ft3', 'cubicfeet' => CubicFoot::class,
			'in3', 'cubicinches' => CubicInch::class,
			'gal', 'gallons' => Gallon::class,
			default => null,
		};
	}

}