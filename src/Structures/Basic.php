<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Structures;

use Rovota\Framework\Support\Interfaces\Arrayable;

abstract class Basic implements Arrayable
{

	protected array $attributes = [];

	// -----------------

	public function __construct(array $attributes = [])
	{
		$this->attributes = $attributes;
	}

	// -----------------

	public function __get(string $name)
	{
		return $this->attributes[$name] ?? null;
	}

	public function __set(string $name, mixed $value)
	{
		$this->attributes[$name] = $value;
	}

	// -----------------

	public function toArray(): array
	{
		return $this->attributes;
	}

}