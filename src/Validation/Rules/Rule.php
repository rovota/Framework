<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules;

use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Validation\Interfaces\RuleInterface;

abstract class Rule implements RuleInterface
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

	public function withOptions(array $options): static
	{
		return $this;
	}

}