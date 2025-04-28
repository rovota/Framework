<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Typing;

use BackedEnum;
use Closure;
use Rovota\Framework\Validation\Rules\Rule;

class EnumRule extends Rule
{

	protected string $enum = BackedEnum::class;

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if ($value instanceof $this->enum === false) {
			$fail('The value must be an enum of the specified type.', data: [
				'target' => $this->enum,
			]);
		}
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (isset($options[0])) {
			$this->enum = $options[0] instanceof BackedEnum ? $options[0]::class : $options[0];
		}

		return $this;
	}

}