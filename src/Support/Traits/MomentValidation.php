<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Support\Traits;

trait MomentValidation
{

	public function isDayOfMonth(int $day): bool
	{
		return (int) $this->format('d') === $day;
	}

	// -----------------

	public function isNight(): bool
	{
		return $this->isBetweenTimes('21:00:00', '5:00:00');
	}

	public function isMorning(): bool
	{
		return $this->isBetweenTimes('5:00:00', '12:00:00');
	}

	public function isAfternoon(): bool
	{
		return $this->isBetweenTimes('12:00:00', '17:00:00');
	}

	public function isEvening(): bool
	{
		return $this->isBetweenTimes('17:00:00', '21:00:00');
	}

	// -----------------

	public function isBetweenTimes(string $start, string $end): bool
	{
		$start = $this->copy()->setTimeFrom($start);
		$end = $this->copy()->setTimeFrom($end);

		if ($start > $end) {
			$end->addDay();
		}

		return $this->between($start, $end);
	}

}