<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Typing;

use Closure;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Validation\Rules\Rule;

class MomentRule extends Rule
{

	public function validate(mixed $value, Closure $fail): void
	{
		if ($value instanceof Moment === false) {
			$fail('The value must be a valid Moment instance.');
		}
	}

}