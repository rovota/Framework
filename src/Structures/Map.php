<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Structures;

class Map extends Collection
{

	public function keys(): Sequence
	{
		return new Sequence(array_values($this->keys));
	}

	public function values(): Sequence
	{
		return new Sequence(array_values($this->values));
	}

	// -----------------

	public function get(mixed $key): mixed
	{
		return $this->offsetGet($key);
	}

	public function remove(mixed $key): void
	{
		$this->offsetUnset($key);
	}

	public function find(mixed $value): mixed
	{
		return $this->retrieveKeyForValue($value);
	}

	public function set(mixed $key, mixed $value): void
	{
		$this->offsetSet($key, $value);
	}

	// -----------------

	public function hasKey(mixed $key): bool
	{
		return array_all(is_array($key) ? $key : [$key], fn($key) => in_array($key, $this->keys, true) === true);
	}

	public function hasValue(mixed $value): bool
	{
		return array_all(is_array($value) ? $value : [$value], fn($value) => in_array($value, $this->values, true) === true);
	}

	// -----------------

	public function slice(int $key, int|null $length = null): static
	{
		return new static(array_slice($this->values, $key, $length, true));
	}

	// -----------------

	public function reverse(): static
	{
		$this->values = array_reverse($this->values);
		$this->keys = array_reverse($this->keys);
		return $this;
	}

	public function sort(callable|null $comparator = null, bool $descending = false): static
	{
		if (is_callable($comparator)) {
			uasort($this->values, $comparator);
		} else {
			$descending ? arsort($this->values) : asort($this->values);
		}

		$this->keys = array_keys($this->values);
		return $this;
	}

}