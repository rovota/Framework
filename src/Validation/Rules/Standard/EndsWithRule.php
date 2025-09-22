<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Standard;

use Closure;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Validation\Rules\Rule;

class EndsWithRule extends Rule
{

	protected string $target = '-';

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if (!is_string($value)) {
			return;
		}

		if (!str_ends_with($value, $this->target)) {
			$fail('The value must end with :target.', data: [
				'actual' => Str::takeLast($value, Str::length($this->target)),
				'target' => $this->target,
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