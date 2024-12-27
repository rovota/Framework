<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Carbon\Carbon;
use Carbon\Month;
use Carbon\WeekDay;
use DateTimeInterface;
use DateTimeZone;
use Rovota\Framework\Localization\LocalizationManager;
use Rovota\Framework\Support\Traits\MomentFormatters;
use Rovota\Framework\Support\Traits\MomentModifiers;
use Rovota\Framework\Support\Traits\MomentValidation;

final class Moment extends Carbon
{
	use MomentModifiers, MomentValidation, MomentFormatters;

	// -----------------

	public function __construct(float|DateTimeInterface|int|string|WeekDay|Month|null $time = null, int|DateTimeZone|string|null $timezone = null)
	{
		if (is_numeric($time)) {
			$time = (int) $time;
		}

		parent::__construct($time, $timezone);
	}

	// -----------------

	public function __toString(): string
	{
		return $this->format();
	}

	// -----------------

	public function format(string|null $format = null, bool $localize = false): string
	{
		if ($localize === true) {
			$this->setTimezone('local');
		}
		return parent::format($format ?? 'Y-m-d H:i:s');
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