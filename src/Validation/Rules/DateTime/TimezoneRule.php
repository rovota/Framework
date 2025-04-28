<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\DateTime;

use Closure;
use DateTimeZone;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Validation\Rules\Rule;

class TimezoneRule extends Rule
{

	protected array $timezones = [];

	// -----------------
	public function validate(mixed $value, Closure $fail): void
	{
		if (empty($this->timezones)) {
			$this->timezones = timezone_identifiers_list();
		}

		if (is_string($value) || $value instanceof DateTimeZone) {
			$value = $value instanceof DateTimeZone ? $value->getName() : $value;
			if (Arr::contains($this->timezones, $value)) {
				return;
			}
		}

		$fail('The value must be a valid timezone.', data: [
			'timezones' => $this->timezones,
		]);
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->timezones = is_string($options[0]) ? [$options[0]] : $options[0];
		}

		return $this;
	}

}