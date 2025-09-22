<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units;

use Rovota\Framework\Conversion\Enums\UnitType;
use Rovota\Framework\Conversion\Units\Time\Day;
use Rovota\Framework\Conversion\Units\Time\Hour;
use Rovota\Framework\Conversion\Units\Time\MicroSecond;
use Rovota\Framework\Conversion\Units\Time\MilliSecond;
use Rovota\Framework\Conversion\Units\Time\Minute;
use Rovota\Framework\Conversion\Units\Time\Second;
use Rovota\Framework\Conversion\Units\Time\Week;
use Rovota\Framework\Conversion\Units\Time\Year;

/**
 * @method static self fromYears(float|int $value)
 * @method static self fromWeeks(float|int $value)
 * @method static self fromDays(float|int $value)
 * @method static self fromHours(float|int $value)
 * @method static self fromMinutes(float|int $value)
 * @method static self fromSeconds(float|int $value)
 * @method static self fromMicroseconds(float|int $value)
 *
 * @method self toYears()
 * @method self toWeeks()
 * @method self toDays()
 * @method self toHours()
 * @method self toMinutes()
 * @method self toSeconds()
 * @method self toMilliseconds()
 * @method self toMicroseconds()
 */
abstract class Time extends Unit
{
	const UnitType UNIT_TYPE = UnitType::Time;

	const string BASE_UNIT = Second::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match ($identifier) {
			'y', 'year', 'years' => Year::class,
			'w', 'week', 'weeks' => Week::class,
			'd', 'day', 'days' => Day::class,
			'h', 'hour', 'hours' => Hour::class,
			'm', 'minute', 'minutes' => Minute::class,
			's', 'second', 'seconds' => Second::class,
			'ms', 'millisecond', 'milliseconds' => MilliSecond::class,
			'μs', 'microsecond', 'microseconds' => MicroSecond::class,
			default => null,
		};
	}

}