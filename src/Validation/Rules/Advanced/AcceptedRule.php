<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Advanced;

use Closure;
use Rovota\Framework\Validation\Rules\Rule;

class AcceptedRule extends Rule
{

	protected string $target = '-';

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if (!filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
			$fail('The value must be considered true.');
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