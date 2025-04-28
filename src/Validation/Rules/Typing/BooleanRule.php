<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Typing;

use Closure;
use Rovota\Framework\Validation\Rules\Rule;

class BooleanRule extends Rule
{

	public function validate(mixed $value, Closure $fail): void
	{
		if (!filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
			$fail('The value must be a valid boolean.');
		}
	}

}