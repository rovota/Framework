<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Typing;

use Closure;
use Rovota\Framework\Validation\Rules\Rule;

class FloatRule extends Rule
{

	public function validate(mixed $value, Closure $fail): void
	{
		if (!is_float($value)) {
			$fail('The value must be a valid floating-point number.');
		}
	}

}