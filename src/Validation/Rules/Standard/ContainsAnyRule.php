<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Standard;

use Closure;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Validation\Rules\Rule;

class ContainsAnyRule extends Rule
{

	protected array $items = [];

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if (is_string($value) && Str::containsAny($value, $this->items) === false) {
			$fail('The value must contain any of the specified items.', data: [
				'items' => $this->items,
			]);
		}

		if (is_array($value) && Arr::containsAny($value, $this->items) === false) {
			$fail('The value must contain any of the specified items.', data: [
				'items' => $this->items,
			]);
		}
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->items = $options;
		}

		return $this;
	}

}