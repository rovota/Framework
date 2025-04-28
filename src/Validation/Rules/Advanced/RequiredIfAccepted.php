<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Advanced;

use Closure;
use Rovota\Framework\Validation\Interfaces\ContextAware;
use Rovota\Framework\Validation\Rules\Rule;

class RequiredIfAccepted extends Rule implements ContextAware
{

	protected string $target = '-';

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if ($this->context->bool($this->target) && $value === null) {
			$fail("A value is required when ':target' is enabled.", data: [
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