<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Interfaces;

use Closure;

interface RuleInterface
{

	public string $name {
		get;
		set;
	}

	// -----------------

	public function validate(mixed $value, Closure $fail): void;

	// -----------------

	public function withOptions(array $options): static;

}