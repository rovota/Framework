<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units;

use Rovota\Framework\Conversion\Enums\UnitType;
use Rovota\Framework\Conversion\Units\Speed\FootPerSecond;
use Rovota\Framework\Conversion\Units\Speed\KiloMeterPerHour;
use Rovota\Framework\Conversion\Units\Speed\Knot;
use Rovota\Framework\Conversion\Units\Speed\MeterPerSecond;
use Rovota\Framework\Conversion\Units\Speed\MilePerHour;

/**
 * @method static self fromMetersPerSecond(float|int $value)
 * @method static self fromKilometersPerHour(float|int $value)
 * @method static self fromFeetPerSecond(float|int $value)
 * @method static self fromMilesPerHour(float|int $value)
 * @method static self fromKnots(float|int $value)
 *
 * @method self toMetersPerSecond()
 * @method self toKilometersPerHour()
 * @method self toFeetPerSecond()
 * @method self toMilesPerHour()
 * @method self toKnots()
 */
abstract class Speed extends Unit
{
	const UnitType UNIT_TYPE = UnitType::Speed;

	const string BASE_UNIT = MeterPerSecond::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match ($identifier) {
			'ms', 'm/s', 'meter per second', 'meterspersecond' => MeterPerSecond::class,
			'kmh', 'km/h', 'kilometer per hour', 'kilometersperhour' => KiloMeterPerHour::class,

			'fts', 'ft/s', 'foot per second', 'feetpersecond' => FootPerSecond::class,
			'mph', 'mile per hour', 'milesperhour' => MilePerHour::class,
			'kn', 'knot', 'knots' => Knot::class,
			default => null,
		};
	}

}