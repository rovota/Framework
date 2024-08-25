<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Support\Traits;

use DateTimeZone;
use ReturnTypeWillChange;
use Rovota\Framework\Localization\LocalizationManager;

trait MomentModifiers
{

	#[ReturnTypeWillChange]
	public function setTimezone(DateTimeZone|string|int $timeZone): static
	{
		if ($timeZone === 'local') {
			$timeZone = LocalizationManager::instance()->getCurrentTimezone();
		}

		parent::setTimezone($timeZone);
		return $this;
	}

	// -----------------

	public function toUtc(): static
	{
		return $this->setTimezone('UTC');
	}

	public function toLocal(): static
	{
		return $this->setTimezone('local');
	}

	// -----------------

	public function nextHour(): static
	{
		return $this->modify('next hour');
	}

	public function nextDay(): static
	{
		return $this->modify('next day');
	}

	public function nextMonday(): static
	{
		return $this->modify('next Monday');
	}

	public function nextTuesday(): static
	{
		return $this->modify('next Tuesday');
	}

	public function nextWednesday(): static
	{
		return $this->modify('next Wednesday');
	}

	public function nextThursday(): static
	{
		return $this->modify('next Thursday');
	}

	public function nextFriday(): static
	{
		return $this->modify('next Friday');
	}

	public function nextSaturday(): static
	{
		return $this->modify('next Saturday');
	}

	public function nextSunday(): static
	{
		return $this->modify('next Sunday');
	}

	// -----------------

	public function previousHour(): static
	{
		return $this->modify('previous hour');
	}

	public function previousDay(): static
	{
		return $this->modify('previous day');
	}

	public function previousMonday(): static
	{
		return $this->modify('previous Monday');
	}

	public function previousTuesday(): static
	{
		return $this->modify('previous Tuesday');
	}

	public function previousWednesday(): static
	{
		return $this->modify('previous Wednesday');
	}

	public function previousThursday(): static
	{
		return $this->modify('previous Thursday');
	}

	public function previousFriday(): static
	{
		return $this->modify('previous Friday');
	}

	public function previousSaturday(): static
	{
		return $this->modify('previous Saturday');
	}

	public function previousSunday(): static
	{
		return $this->modify('previous Sunday');
	}

}