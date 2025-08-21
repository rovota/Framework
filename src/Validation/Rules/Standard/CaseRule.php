<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Standard;

use Closure;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Validation\Rules\Rule;

class CaseRule extends Rule
{

	protected string $casing = '-';

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if (!is_string($value)) {
			return;
		}

		$matches = match ($this->casing) {
			'camel' => Str::camel($value) === $value,
			'kebab' => Str::kebab($value) === $value,
			'lower' => Str::lower($value) === $value,
			'pascal' => Str::pascal($value) === $value,
			'snake' => Str::snake($value) === $value,
			'title' => Str::title($value) === $value,
			'upper' => Str::upper($value) === $value,
			default => true
		};

		if ($matches === false) {
			$fail('The value must follow the specified casing.', data: [
				'casing' => $this->casing,
			]);
		}
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (isset($options[0])) {
			$this->casing = $options[0];
		}

		return $this;
	}

}