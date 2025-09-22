<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units;

use Rovota\Framework\Conversion\Enums\UnitType;
use Rovota\Framework\Conversion\Units\Frequency\GigaHertz;
use Rovota\Framework\Conversion\Units\Frequency\Hertz;
use Rovota\Framework\Conversion\Units\Frequency\KiloHertz;
use Rovota\Framework\Conversion\Units\Frequency\MegaHertz;

/**
 * @method static self fromHertz(float|int $value)
 * @method static self fromKilohertz(float|int $value)
 * @method static self fromMegahertz(float|int $value)
 * @method static self fromGigahertz(float|int $value)
 *
 * @method self toHertz()
 * @method self toKilohertz()
 * @method self toMegahertz()
 * @method self toGigahertz()
 */
abstract class Frequency extends Unit
{
	const UnitType UNIT_TYPE = UnitType::Frequency;

	const string BASE_UNIT = Hertz::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match ($identifier) {
			'hz', 'hertz' => Hertz::class,
			'khz', 'kilohertz' => KiloHertz::class,
			'mhz', 'megahertz' => MegaHertz::class,
			'ghz', 'gigahertz' => GigaHertz::class,
			default => null,
		};
	}

}