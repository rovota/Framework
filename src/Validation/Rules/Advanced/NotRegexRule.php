<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Advanced;

use Closure;
use Rovota\Framework\Validation\Rules\Rule;

class NotRegexRule extends Rule
{

	protected string $pattern = 'now';

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if (preg_match($this->pattern, $value)) {
			$fail('The value does not match an allowed pattern.', data: [
				'pattern' => $this->pattern,
			]);
		}
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (isset($options[0])) {
			$this->pattern = $options[0];
		}

		return $this;
	}

}