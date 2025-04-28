<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\DateTime;

use Closure;
use DateTime;
use Rovota\Framework\Validation\Rules\Rule;

class DateFormatRule extends Rule
{

	protected array $formats = [];

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		foreach ($this->formats as $format) {
			$date = DateTime::createFromFormat($format, $value);
			if ($date && $date->format($format) === $value) {
				return;
			}
		}

		$fail('The value must follow a specified format.', data: [
			'formats' => $this->formats,
		]);
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->formats = $options;
		}

		return $this;
	}

}