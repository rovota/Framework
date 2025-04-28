<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Advanced;

use Closure;
use Rovota\Framework\Validation\Interfaces\ContextAware;
use Rovota\Framework\Validation\Rules\Rule;

class EqualRule extends Rule implements ContextAware
{

	protected string $target = '-';

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if ($this->context->get($this->target) !== $value) {
			$fail('The value must be equal to :target.', data: [
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