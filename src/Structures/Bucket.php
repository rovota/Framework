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
use Dflydev\DotAccessData\Data;
use Dflydev\DotAccessData\DataInterface;
use IteratorAggregate;
use JsonSerializable;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Interfaces\Arrayable;
use Rovota\Framework\Support\Traits\TypeAccessors;
use Traversable;

class Bucket implements ArrayAccess, IteratorAggregate, Countable, Arrayable, JsonSerializable
{
	use TypeAccessors;

	protected Data $items;

	// -----------------

	public function __construct(mixed $items = [])
	{
		$this->items = new Data(convert_to_array($items));
	}

	public function import(mixed $data, bool $preserve = false): static
	{
		$mode = $preserve ? DataInterface::PRESERVE : DataInterface::MERGE;
		$this->items->import(convert_to_array($data), $mode);
		return $this;
	}

	// -----------------

	public function keys(): Sequence
	{
		return new Sequence(array_keys($this->toArray()));
	}

	public function values(): Sequence
	{
		return new Sequence(array_values($this->toArray()));
	}

	// -----------------

	public function flush(): static
	{
		$this->items = new Data();
		return $this;
	}

	public function copy(): static
	{
		return clone $this;
	}

	public function count(mixed $key = null): int
	{
		return count($key !== null ? ($this->get($key) ?? []) : $this->toArray());
	}

	// -----------------

	public function isEmpty(): bool
	{
		return empty($this->toArray());
	}

	// -----------------

	public function all(): array
	{
		return $this->toArray();
	}

	public function has(mixed $key): bool
	{
		foreach (is_array($key) ? $key : [$key] as $item) {
			if ($this->offsetExists($item) === false) {
				return false;
			}
		}
		return true;
	}

	public function missing(mixed $key): bool
	{
		foreach (is_array($key) ? $key : [$key] as $item) {
			if ($this->offsetExists($item) === true) {
				return false;
			}
		}
		return true;
	}

	public function pull(mixed $key, mixed $default = null): mixed
	{
		$value = $this->offsetGet($key) ?? $default;
		$this->offsetUnset($key);
		return $value;
	}

	public function get(mixed $key, mixed $default = null): mixed
	{
		return $this->offsetGet($key) ?? ($default instanceof Closure ? $default() : $default);
	}

	public function remove(mixed $key): static
	{
		foreach (is_array($key) ? $key : [$key] as $item) {
			$this->offsetUnset($item);
		}
		return $this;
	}

	public function find(mixed $value): string|int|null
	{
		return Arr::search($this->toArray(), $value);
	}

	public function set(mixed $key, mixed $value = null): static
	{
		if (is_array($key)) {
			foreach ($key as $offset => $item) {
				$this->offsetSet($offset, $item);
			}
		} else {
			$this->offsetSet($key, $value);
		}
		return $this;
	}

	// -----------------

	public function append(mixed $value): static
	{
		foreach (is_array($value) ? $value : [$value] as $item) {
			$this->offsetSet(null, $item);
		}
		return $this;
	}

	public function prepend(mixed $value): static
	{
		$original = $this->toArray();
		array_unshift($original, $value);
		$this->items = new Data($original);
		return $this;
	}

	// -----------------

	public function first(callable|null $callback = null, mixed $default = null): mixed
	{
		return Arr::first($this->toArray(), $callback, $default);
	}

	public function last(callable|null $callback = null, mixed $default = null): mixed
	{
		return Arr::last($this->toArray(), $callback, $default);
	}

	// -----------------

	/**
	 * Only returns items that match a given truth test.
	 */
	public function filter(callable $callback): static
	{
		return new static(Arr::filter($this->toArray(), $callback));
	}

	/**
	 * Returns all items, except for those with the key(s) given.
	 */
	public function except(array $keys): static
	{
		return $this->copy()->remove($keys);
	}

	/**
	 * Returns only the items which are specified by key.
	 */
	public function only(array $keys): static
	{
		$bucket = new static();
		foreach ($keys as $key) {
			$bucket->set($key, $this->offsetGet($key));
		}
		return $bucket;
	}

	// -----------------

	public function increment(mixed $key, int $step = 1): static
	{
		$this->set($key, (int) $this->get($key, 0) + abs($step));
		return $this;
	}

	public function decrement(mixed $key, int $step = 1): static
	{
		$this->set($key, (int) $this->get($key, 0) - abs($step));
		return $this;
	}

	// -----------------

	public function toArray(): array
	{
		return $this->items->export();
	}

	public function toJson(): string
	{
		return json_encode_clean($this->items->export());
	}

	public function toMap(): Map
	{
		return new Map($this->items->export());
	}

	public function toSequence(): Sequence
	{
		return new Sequence($this->items->export());
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
		return new ArrayIterator($this->items->export());
	}

	/**
	 * @internal
	 */
	public function offsetExists(mixed $offset): bool
	{
		if (is_object($offset)) {
			$offset = spl_object_hash($offset);
		}
		return $this->items->has($offset);
	}

	/**
	 * @internal
	 */
	public function offsetGet(mixed $offset): mixed
	{
		if (is_object($offset)) {
			$offset = spl_object_hash($offset);
		}

		if ($this->offsetExists($offset)) {
			return $this->items->get($offset);
		}

		return null;
	}

	/**
	 * @internal
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		if ($offset === null) {
			$items = $this->toArray();
			$items[] = $value;
			$this->items = new Data($items);
		} else {
			if (is_object($offset)) {
				$offset = spl_object_hash($offset);
			}
			$this->items->set($offset, $value);
		}
	}

	/**
	 * @internal
	 */
	public function offsetUnset(mixed $offset): void
	{
		if (is_object($offset)) {
			$offset = spl_object_hash($offset);
		}
		$this->items->remove($offset);
	}

}