<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Structures;

use TypeError;

class Sequence extends Collection
{

	public function __construct(mixed $items = [])
	{
		parent::__construct(array_values(convert_to_array($items)));
	}

	// -----------------

	public function has(mixed $index): bool
	{
		foreach (is_array($index) ? $index : [$index] as $index) {
			if ($this->offsetExists($index) === false) {
				return false;
			}
		}
		return true;
	}

	public function get(int $index): mixed
	{
		return $this->offsetGet($index);
	}

	public function remove(int $index): void
	{
		$this->offsetUnset($index);
	}

	public function find(mixed $value): int|null
	{
		return $this->retrieveKeyForValue($value);
	}

	public function set(int $index, mixed $value): void
	{
		$this->offsetSet($index, $value);
	}

	// -----------------

	public function insert(int $index, array $values): void
	{
		array_splice( $this->values, $index, 0, $values);
		$this->keys = array_keys($this->values);
	}

	public function append(array $values): void
	{
		foreach ($values as $value) {
			$this->offsetSet(null, $value);
		}
	}

	public function prepend(array $values): void
	{
		$this->insert(0, $values);
	}

	// -----------------

	public function shift(): mixed
	{
		if ($this->isEmpty()) {
			return null;
		}

		$value = $this->first();
		$this->offsetUnset($this->retrieveKeyForValue($value));
		return $value;
	}

	public function pop(): mixed
	{
		if ($this->isEmpty()) {
			return null;
		}

		$value = $this->last();
		$this->offsetUnset($this->retrieveKeyForValue($value));
		return $value;
	}

	public function slice(int $index, int|null $length = null): Sequence
	{
		return new Sequence(array_slice($this->values, $index, $length));
	}

	public function reverse(): static
	{
		$this->values = array_reverse($this->values);
		return $this;
	}

	// -----------------

	public function sort(callable|null $comparator = null, bool $descending = false): Sequence
	{
		if (is_callable($comparator)) {
			uasort($this->values, $comparator);
		} else {
			$descending ? arsort($this->values) : asort($this->values);
		}

		$this->values = array_values($this->values);
		$this->keys = array_keys($this->values);
		return $this;
	}

	// -----------------

	public function offsetExists(mixed $offset): bool
	{
		if (is_int($offset) === false) {
			throw new TypeError('Only integers are allowed to be used as keys within a Sequence.');
		}
		return parent::offsetExists($offset);
	}

	public function offsetGet(mixed $offset): mixed
	{
		if (is_int($offset) === false) {
			throw new TypeError('Only integers are allowed to be used as keys within a Sequence.');
		}
		return $this->values[$offset] ?? null;
	}

	public function offsetSet(mixed $offset, mixed $value): void
	{
		if ($offset === null) {
			$this->values[] = $value;
			$this->keys[] = array_key_last($this->values);
		} else {
			if (is_int($offset) === false) {
				throw new TypeError('Only integers are allowed to be used as keys within a Sequence.');
			}
			$this->values[$offset] = $value;
			if (in_array($offset, $this->keys) === false) {
				$this->keys[$offset] = $offset;
			}
		}
	}

	public function offsetUnset(mixed $offset): void
	{
		if (is_int($offset) === false) {
			throw new TypeError('Only integers are allowed to be used as keys within a Sequence.');
		}
		unset($this->values[$offset]);
		unset($this->keys[$offset]);

		$this->values = array_values($this->values);
		$this->keys = array_keys($this->values);
	}

}