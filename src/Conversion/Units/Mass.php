<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units;

use Rovota\Framework\Conversion\Enums\UnitType;
use Rovota\Framework\Conversion\Units\Mass\Gram;
use Rovota\Framework\Conversion\Units\Mass\KiloGram;
use Rovota\Framework\Conversion\Units\Mass\MilliGram;
use Rovota\Framework\Conversion\Units\Mass\Ounce;
use Rovota\Framework\Conversion\Units\Mass\Pound;
use Rovota\Framework\Conversion\Units\Mass\Tonne;

/**
 * @method static self fromTonnes(float|int $value)
 * @method static self fromKilograms(float|int $value)
 * @method static self fromGrams(float|int $value)
 * @method static self fromMilligrams(float|int $value)
 * @method static self fromOunces(float|int $value)
 * @method static self fromPounds(float|int $value)
 *
 * @method self toTonnes()
 * @method self toKilograms()
 * @method self toGrams()
 * @method self toMilligrams()
 * @method self toOunces()
 * @method self toPounds()
 */
abstract class Mass extends Unit
{
	const UnitType UNIT_TYPE = UnitType::Mass;

	const string BASE_UNIT = Gram::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match ($identifier) {
			't', 'tonne', 'tonnes' => Tonne::class,
			'kg', 'kilogram', 'kilograms' => KiloGram::class,
			'g', 'gram', 'grams' => Gram::class,
			'mg', 'milligram', 'milligrams' => MilliGram::class,

			'oz', 'ounce', 'ounces' => Ounce::class,
			'lb', 'pound', 'pounds' => Pound::class,
			default => null,
		};
	}

}