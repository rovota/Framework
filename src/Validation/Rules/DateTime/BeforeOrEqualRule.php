<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\DateTime;

use Closure;
use Rovota\Framework\Validation\Rules\Rule;

class BeforeOrEqualRule extends Rule
{

	protected mixed $target = 'now';

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if (!moment($value)->lessThanOrEqualTo($this->target)) {
			$fail('The value must be on or before the specified date.', data: [
				'target' => moment($this->target),
			]);
		}
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (isset($options[0])) {
			$this->target = $options[0];
		}

		return $this;
	}

}