<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Carbon\Carbon;
use DateTimeZone;
use ReturnTypeWillChange;
use Rovota\Framework\Localization\Localization;
use Rovota\Framework\Support\Traits\MomentModifiers;
use Rovota\Framework\Support\Traits\MomentValidation;

final class Moment extends Carbon
{
	use MomentModifiers, MomentValidation;

	// -----------------

	public function __toString(): string
	{
		return $this->format();
	}

	// -----------------

	/** @noinspection PhpUnnecessaryStaticReferenceInspection */
	#[ReturnTypeWillChange]
	public function setTimezone(DateTimeZone|string|int $timeZone): static
	{
		if ($timeZone === 'local') {
			$timeZone = Localization::getActiveTimezone();
		}

		parent::setTimezone($timeZone);
		return $this;
	}

	// -----------------

	public function format(string|null $format = null, bool $localize = false): string
	{
		if ($localize === true) {
			$this->setTimezone('local');
		}
		return parent::format($format ?? 'Y-m-d H:i:s');
	}

	public function toEpochString(): string
	{
		return $this->format('U');
	}

	// -----------------

	public function getTimeOfDayPeriod(): int
	{
		return match (true) {
			$this->isNight() => 0,
			$this->isMorning() => 1,
			$this->isAfternoon() => 2,
			$this->isEvening() => 3,
		};
	}

}