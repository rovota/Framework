<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules;

use Closure;
use Rovota\Framework\Structures\Bucket;

abstract class Rule
{

	public string $name;

	public Bucket $context;

	// -----------------

	public function __construct(string $name)
	{
		$this->name = $name;
		$this->context = new Bucket();
	}

	// -----------------

	abstract public function validate(mixed $value, Closure $fail): void;

	// -----------------

	public function withOptions(array $options): static
	{
		return $this;
	}

}