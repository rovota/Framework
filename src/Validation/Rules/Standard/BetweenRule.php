<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Standard;

use Closure;
use Rovota\Framework\Validation\Rules\Rule;
use Rovota\Framework\Validation\ValidationTools;

class BetweenRule extends Rule
{

	protected float|int $min = 0;

	protected float|int $max = 0;

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		$actual = ValidationTools::getSize($value);

		if ($actual <= $this->min || $actual >= $this->max) {
			$fail('The value must be between :min and :max.', data: [
				'actual' => $actual,
				'min' => $this->min,
				'max' => $this->max,
			]);
		}
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (count($options) === 2) {
			$this->min = $options[0];
			$this->max = $options[1];
		}

		return $this;
	}

}