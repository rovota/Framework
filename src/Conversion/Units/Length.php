<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units;

use Rovota\Framework\Conversion\Enums\UnitType;
use Rovota\Framework\Conversion\Units\Length\CentiMeter;
use Rovota\Framework\Conversion\Units\Length\DeciMeter;
use Rovota\Framework\Conversion\Units\Length\Foot;
use Rovota\Framework\Conversion\Units\Length\HectoMeter;
use Rovota\Framework\Conversion\Units\Length\Inch;
use Rovota\Framework\Conversion\Units\Length\KiloMeter;
use Rovota\Framework\Conversion\Units\Length\Meter;
use Rovota\Framework\Conversion\Units\Length\MicroMeter;
use Rovota\Framework\Conversion\Units\Length\Mile;
use Rovota\Framework\Conversion\Units\Length\MilliMeter;
use Rovota\Framework\Conversion\Units\Length\NanoMeter;
use Rovota\Framework\Conversion\Units\Length\NauticalMile;
use Rovota\Framework\Conversion\Units\Length\Yard;

/**
 * @method static self fromKilometers(float|int $value)
 * @method static self fromHectometers(float|int $value)
 * @method static self fromMeters(float|int $value)
 * @method static self fromDecimeters(float|int $value)
 * @method static self fromCentimeters(float|int $value)
 * @method static self fromMillimeters(float|int $value)
 * @method static self fromMicrometers(float|int $value)
 * @method static self fromNanometers(float|int $value)
 * @method static self fromMiles(float|int $value)
 * @method static self fromYards(float|int $value)
 * @method static self fromFeet(float|int $value)
 * @method static self fromInches(float|int $value)
 * @method static self fromNauticalMiles(float|int $value)
 *
 * @method self toKilometers()
 * @method self toHectometers()
 * @method self toMeters()
 * @method self toDecimeters()
 * @method self toCentimeters()
 * @method self toMillimeters()
 * @method self toMicrometers()
 * @method self toNanometers()
 * @method self toMiles()
 * @method self toYards()
 * @method self toFeet()
 * @method self toInches()
 * @method self toNauticalMiles()
 */
abstract class Length extends Unit
{
	const UnitType UNIT_TYPE = UnitType::Length;

	const string BASE_UNIT = Meter::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match ($identifier) {
			'km', 'kilometer', 'kilometers' => KiloMeter::class,
			'hm', 'hectometer', 'hectometers' => HectoMeter::class,
			'm', 'meter', 'meters' => Meter::class,
			'dm', 'decimeter', 'decimeters' => DeciMeter::class,
			'cm', 'centimeter', 'centimeters' => CentiMeter::class,
			'mm', 'millimeter', 'millimeters' => MilliMeter::class,
			'μm', 'micrometer', 'micrometers' => MicroMeter::class,
			'nm', 'nanometer', 'nanometers' => NanoMeter::class,

			'mi', 'mile', 'miles' => Mile::class,
			'yd', 'yard', 'yards' => Yard::class,
			'ft', 'foot', 'feet' => Foot::class,
			'in', 'inch', 'inches' => Inch::class,
			'NM', 'nautical mile', 'nauticalmiles' => NauticalMile::class,
			default => null,
		};
	}

}