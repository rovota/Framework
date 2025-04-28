<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Advanced;

use Closure;
use Rovota\Framework\Validation\Interfaces\ContextAware;
use Rovota\Framework\Validation\Rules\Rule;

class RequiredIfDeclined extends Rule implements ContextAware
{

	protected string $target = '-';

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if ($this->context->bool($this->target) === false && $value === null) {
			$fail("A value is required when ':target' is disabled.", data: [
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