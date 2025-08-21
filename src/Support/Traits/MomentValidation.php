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
		return (int)$this->format('d') === $day;
	}

	// -----------------

	public function isNight(): bool
	{
		return $this->isTimeBetween('21:00:00', '5:00:00');
	}

	public function isMorning(): bool
	{
		return $this->isTimeBetween('5:00:00', '12:00:00');
	}

	public function isAfternoon(): bool
	{
		return $this->isTimeBetween('12:00:00', '17:00:00');
	}

	public function isEvening(): bool
	{
		return $this->isTimeBetween('17:00:00', '21:00:00');
	}

	// -----------------

	public function isTimeBefore(mixed $target): bool
	{
		return $this->format('Gis.u') < moment($target)->format('Gis.u');
	}

	public function isTimeAfter(mixed $target): bool
	{
		return $this->format('Gis.u') > moment($target)->format('Gis.u');
	}

	// -----------------

	public function isTimeBetween(string $start, string $end): bool
	{
		return $this->isTimeAfter($start) || $this->isTimeBefore($end);
	}

}