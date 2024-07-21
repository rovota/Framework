<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Structures;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Rovota\Framework\Kernel\Application;
use Rovota\Framework\Support\Interfaces\Arrayable;
use Rovota\Framework\Support\Math;
use Traversable;

abstract class Collection implements ArrayAccess, IteratorAggregate, Countable, Arrayable, JsonSerializable
{

	protected array $values;
	protected array $keys;

	protected int $key_object_count = 0;

	// -----------------

	public function __construct(mixed $items = [])
	{
		$this->values = convert_to_array($items);
		$this->keys = array_keys($this->values);
	}

	// -----------------

	public static function from(mixed $items = []): static
	{
		return new static($items);
	}

	// -----------------

	public function clear(): static
	{
		$this->values = [];
		$this->keys = [];
		$this->key_object_count = 0;
		return $this;
	}

	public function copy(): static
	{
		return clone $this;
	}

	public function count(): int
	{
		return count($this->keys);
	}

	// -----------------

	public function min(float|int|null $limit = null): float|int
	{
		return Math::min($this->values, $limit);
	}

	public function max(float|int|null $limit = null): float|int
	{
		return Math::max($this->values, $limit);
	}

	public function average(int $precision = Application::DEFAULT_FLOAT_PRECISION): float|int
	{
		return Math::average($this->values, $precision);
	}

	public function sum(int $precision = Application::DEFAULT_FLOAT_PRECISION): float|int
	{
		return Math::sum($this->values, $precision);
	}

	// -----------------

	public function isEmpty(): bool
	{
		return empty($this->keys);
	}

	// -----------------

	public function first(callable|null $callback = null): mixed
	{
		if (empty($this->values)) {
			return null;
		}
		if ($callback === null) {
			foreach ($this->values as $value) {
				return $value;
			}
		}
		foreach ($this->values as $key => $value) {
			if ($callback($value, $this->keys[$key])) {
				return $value;
			}
		}
		return null;
	}

	public function last(callable|null $callback = null): mixed
	{
		if (empty($this->values)) {
			return null;
		}
		if ($callback === null) {
			return end($this->values);
		}
		foreach (array_reverse($this->values, true) as $key => $value) {
			if ($callback($value, $this->keys[$key])) {
				return $value;
			}
		}
		return null;
	}

	// -----------------

	public function contains(mixed $values): bool
	{
		if (is_array($values)) {
			foreach ($values as $value) {
				if ($this->contains($value) === false) {
					return false;
				}
			}
			return true;
		}

		if ($values instanceof Closure) {
			foreach ($this->values as $index => $value) {
				if ($values($value, $this->keys[$index])) {
					return true;
				}
			}
			return true;
		}
		return in_array($values, $this->values, true);
	}

	// -----------------

	public function filter(callable $callback): static
	{
		$filtered = [];
		foreach ($this->values as $key => $value) {
			if ($callback($value, $this->keys[$key]) === true) {
				$filtered[$key] = $value;
			}
		}
		return new static($filtered);
	}

	// -----------------

	public function join(string $glue, string|null $final_glue = null): string
	{
		if ($final_glue === null) {
			return implode($glue, $this->values);
		}

		if ($this->count() <= 1) {
			return (string) $this->first() ?? '';
		}

		$sequence = new Sequence($this->values);
		$final_item = (string) $sequence->pop();

		return $sequence->join($glue).$final_glue.$final_item;
	}

	// -----------------

	public function toArray(): array
	{
		if ($this->key_object_count === 0) {
			return array_combine($this->keys, $this->values);
		} else {
			return $this->values;
		}
	}

	public function toJson(): string
	{
		return json_encode_clean($this->toArray());
	}

	public function toMap(): Map
	{
		return new Map($this->toArray());
	}

	public function toSequence(): Sequence
	{
		return new Sequence($this->toArray());
	}

	public function toBucket(): Basket
	{
		return new Basket($this->toArray());
	}

	// -----------------

	/**
	 * @internal
	 */
	public function jsonSerialize(): array
	{
		return $this->toArray();
	}

	/**
	 * @internal
	 */
	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->toArray());
	}

	/**
	 * @internal
	 */
	public function offsetExists(mixed $offset): bool
	{
		if (is_object($offset)) {
			$offset = spl_object_hash($offset);
		}
		return isset($this->values[$offset]);
	}

	/**
	 * @internal
	 */
	public function offsetGet(mixed $offset): mixed
	{
		if (is_object($offset)) {
			$offset = spl_object_hash($offset);
		}
		return $this->values[$offset] ?? null;
	}

	/**
	 * @internal
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		if ($offset === null) {
			$this->values[] = $value;
			$this->keys[] = array_key_last($this->values);
		} else {
			if (is_object($offset)) {
				$this->key_object_count++;
				$hash = spl_object_hash($offset);
				$this->values[$hash] = $value;
				$this->keys[$hash] = $offset;
			} else {
				$this->values[$offset] = $value;
				if (in_array($offset, $this->keys) === false) {
					$this->keys[$offset] = $offset;
				}
			}
		}
	}

	/**
	 * @internal
	 */
	public function offsetUnset(mixed $offset): void
	{
		if (is_object($offset)) {
			$offset = spl_object_hash($offset);
			if (isset($this->values[$offset])) {
				$this->key_object_count--;
			}
		}
		unset($this->values[$offset]);
		unset($this->keys[$offset]);
	}

	// -----------------

	protected function retrieveKeyForValue(mixed $value): mixed
	{
		return $this->keys[array_search($value, $this->values)] ?? null;
	}

	protected function retrieveValueForKey(mixed $key): mixed
	{
		return $this->values[array_search($key, $this->keys)] ?? null;
	}

}