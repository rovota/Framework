<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Advanced;

use Closure;
use Rovota\Framework\Validation\Rules\Rule;

class EmailRule extends Rule
{

	public function validate(mixed $value, Closure $fail): void
	{
		if (filter_var($value, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE) === null) {
			$fail('The value must be a valid email address.');
		}
	}

}