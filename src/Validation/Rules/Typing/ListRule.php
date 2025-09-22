<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Typing;

use Closure;
use Rovota\Framework\Validation\Rules\Rule;

class ListRule extends Rule
{

	public function validate(mixed $value, Closure $fail): void
	{
		if (!array_is_list($value)) {
			$fail('The value must be a valid list.');
		}
	}

}