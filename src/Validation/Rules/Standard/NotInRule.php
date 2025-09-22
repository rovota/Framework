<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Standard;

use Closure;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Validation\Rules\Rule;

class NotInRule extends Rule
{

	protected array $items = [];

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if (count($this->items) === 1 && str_contains($this->items[0], '\\')) {
			if ($this->items[0]::tryFrom($value ?? '-') !== null) {
				$fail('The value may not be one of the specified items.');
			}
		}

		if (count($this->items) > 1 && Arr::contains($this->items, $value)) {
			$fail('The value may not be one of the specified items.', data: [
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