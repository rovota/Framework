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
use Rovota\Framework\Support\Arr;
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
		$this->values = Arr::from($items);
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

	public function average(int $precision = 14): float|int
	{
		return Math::average($this->values, $precision);
	}

	public function sum(int $precision = 14): float|int
	{
		return Math::sum($this->values, $precision);
	}

	// -----------------

	public function isEmpty(): bool
	{
		return empty($this->keys);
	}

	public function isNotEmpty(): bool
	{
		return empty($this->keys) === false;
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

		return array_find($this->values, fn($value, $key) => $callback($value, $this->keys[$key]));
	}

	public function firstAndRemove(callable|null $callback = null): mixed
	{
		$result = $this->first($callback);

		if ($result !== null) {
			$this->remove($this->retrieveKeyForValue($result));
		}

		return $result;
	}

	public function last(callable|null $callback = null): mixed
	{
		if (empty($this->values)) {
			return null;
		}
		if ($callback === null) {
			return end($this->values);
		}

		return array_find(array_reverse($this->values, true), fn($value, $key) => $callback($value, $this->keys[$key]));
	}

	public function lastAndRemove(callable|null $callback = null): mixed
	{
		$result = $this->last($callback);

		if ($result !== null) {
			$this->remove($this->retrieveKeyForValue($result));
		}

		return $result;
	}

	// -----------------

	public function contains(mixed $values): bool
	{
		if (is_array($values)) {
			return array_all($values, fn($value) => $this->contains($value) === true);
		}

		if ($values instanceof Closure) {
			return array_any($this->values, fn($value, $key) => $values($value, $this->keys[$key]));
		}

		return in_array($values, $this->values, true);
	}

	// -----------------

	public function filter(callable $callback): static
	{
		return new static(array_filter($this->values, function ($value, $key) use ($callback) {
			return $callback($value, $this->keys[$key]) === true;
		}, ARRAY_FILTER_USE_BOTH));
	}

	// -----------------

	public function join(string $glue, string|null $final_glue = null): string
	{
		if ($final_glue === null) {
			return implode($glue, $this->values);
		}

		if ($this->count() <= 1) {
			return (string)$this->first() ?? '';
		}

		$sequence = new Sequence($this->values);
		$final_item = (string)$sequence->pop();

		return $sequence->join($glue) . $final_glue . $final_item;
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

	public function toBucket(): Bucket
	{
		return new Bucket($this->toArray());
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