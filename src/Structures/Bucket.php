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
use Rovota\Framework\Structures\Traits\TypeAccessors;
use Rovota\Framework\Structures\Traits\ValueAccessors;
use Rovota\Framework\Structures\Traits\ValueModifiers;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Interfaces\Arrayable;
use Rovota\Framework\Support\Internal;
use Rovota\Framework\Support\Traits\Conditionable;
use Rovota\Framework\Support\Traits\Macroable;
use Stringable;
use Traversable;

class Bucket implements ArrayAccess, IteratorAggregate, Countable, Arrayable, JsonSerializable
{
	use Macroable;
	use Conditionable;
	use TypeAccessors;
	use ValueAccessors;
	use ValueModifiers;

	protected Data $items;

	// -----------------

	public function __construct(mixed $items = [])
	{
		$this->items = new Data(Arr::from($items));
	}

	public static function from(mixed $items = []): static
	{
		return new static($items);
	}

	public function import(mixed $data, bool $preserve = false): static
	{
		$mode = $preserve ? DataInterface::PRESERVE : DataInterface::MERGE;
		$this->items->import(Arr::from($data), $mode);
		return $this;
	}

	public function flush(): static
	{
		$this->items = new Data();
		return $this;
	}

	public function copy(): static
	{
		return clone $this;
	}

	// -----------------

	public function isEmpty(): bool
	{
		return empty($this->toArray());
	}

	public function isNotEmpty(): bool
	{
		return empty($this->toArray()) === false;
	}

	// -----------------

	public function all(): array
	{
		return $this->toArray();
	}

	public function keys(): Sequence
	{
		return new Sequence(array_keys($this->toArray()));
	}

	public function values(): Sequence
	{
		return new Sequence(array_values($this->toArray()));
	}

	// -----------------

	public function count(mixed $key = null): int
	{
		return count($key !== null ? ($this->get($key) ?? []) : $this->toArray());
	}

	public function countBy(callable|null $callback = null): static
	{
		$counted = new static();

		if ($callback === null) {
			$counted->import(array_count_values($this->toArray()));
		} else {
			foreach ($this->toArray() as $key => $value) {
				$counted->increment($callback($value, $key));
			}
		}

		return $counted;
	}

	/**
	 * Count how often a given value appears.
	 */
	public function occurrences(string $value): int
	{
		return $this->countBy()[$value] ?? 0;
	}

	// -----------------

	public function has(mixed $key): bool
	{
		return array_all(is_array($key) ? $key : [$key], fn($item) => $this->offsetExists($item) === true);
	}

	public function missing(mixed $key): bool
	{
		return array_all(is_array($key) ? $key : [$key], fn($item) => $this->offsetExists($item) === false);
	}

	public function get(mixed $key, mixed $default = null): mixed
	{
		return $this->offsetGet($key) ?? ($default instanceof Closure ? $default() : $default);
	}

	public function find(mixed $value): string|int|bool
	{
		return Arr::search($this->toArray(), $value);
	}

	public function pull(mixed $key, mixed $default = null): mixed
	{
		$value = $this->offsetGet($key) ?? $default;
		$this->offsetUnset($key);
		return $value;
	}

	public function remove(mixed $key): static
	{
		foreach (is_array($key) ? $key : [$key] as $item) {
			$this->offsetUnset($item);
		}
		return $this;
	}

	// -----------------

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

	public function concat(mixed $data): static
	{
		foreach (Arr::from($data) as $item) {
			$this->append($item);
		}
		return $this;
	}

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

	/**
	 * Determines whether the given value is present. Passing a Closure will check for values matching a given truth test, returning true when at least one is found.
	 */
	public function contains(mixed $value): bool
	{
		return Arr::contains($this->toArray(), $value);
	}

	/**
	 * Determines whether any of the given values are present. Returns true when at least one is found.
	 */
	public function containsAny(array $values): bool
	{
		return Arr::containsAny($this->toArray(), $values);
	}

	/**
	 * Determines whether all items match a given truth test.
	 */
	public function every(callable $callback): bool
	{
		return array_all($this->toArray(), fn($value, $key) => $callback($value, $key) === true);
	}

	// -----------------

	/**
	 * Returns a new bucket with each entry only containing the specified field, optionally keyed by the given key.
	 */
	public function pluck(string $field, string|null $key = null): static
	{
		return new static(Arr::pluck($this->toArray(), $field, $key));
	}

	// -----------------

	public function skip(int $count): static
	{
		if ($this->count() <= $count) {
			return new static();
		}

		$iterations = 0;
		$result = $this->copy();

		foreach ($this->toArray() as $key => $value) {
			if ($iterations === $count) {
				break;
			}
			$result->remove($key);
			$iterations++;
		}
		return $result;
	}

	public function skipUntil(mixed $target): static
	{
		if ($this->count() === 0) {
			return new static();
		}

		$result = $this->copy();

		foreach ($this->toArray() as $key => $value) {
			if (($target instanceof Closure && $target($value, $key)) || $target === $value) {
				break;
			}
			$result->remove($key);
		}
		return $result;
	}

	// -----------------

	public function shuffle(): static
	{
		$this->items = new Data(Arr::shuffle($this->toArray()));
		return $this;
	}

	public function reverse(): static
	{
		$this->items = new Data(array_reverse($this->toArray(), true));
		return $this;
	}

	public function sort(callable|null $callback = null, bool $descending = false): static
	{
		$this->items = new Data(Arr::sort($this->toArray(), $callback, $descending));
		return $this;
	}

	public function sortBy(mixed $callback, bool $descending = false): static
	{
		$this->items = new Data(Arr::sortBy($this->toArray(), $callback, $descending));
		return $this;
	}

	public function sortKeys(bool $descending = false): static
	{
		$this->items = new Data(Arr::sortKeys($this->toArray(), $descending));
		return $this;
	}

	// -----------------

	public function collapse(): static
	{
		$collapsed = Arr::collapse($this->toArray());
		$this->items = new Data($collapsed);
		return $this;
	}

	public function implode(string $value, string|null $glue = null): string
	{
		$first = $this->first();

		if ((is_array($first) || $first instanceof Arrayable) && !$first instanceof Stringable) {
			return implode($glue ?? '', $this->pluck($value)->toArray());
		}

		return implode($value, $this->toArray());
	}

	public function join(string $glue = '', string|null $final_glue = null): string
	{
		if ($final_glue === null) {
			return $this->implode($glue);
		}

		$count = $this->count();

		if ($count === 0) {
			return '';
		}

		if ($count === 1) {
			return (string)$this->first();
		}

		$bucket = new static($this->items);
		$final_item = (string)$bucket->pop();

		return $bucket->implode($glue) . $final_glue . $final_item;
	}

	public function groupBy(callable|string $group_by, bool $preserve_keys = false): static
	{
		$group_by = Internal::valueRetriever($group_by);
		$results = new static();

		foreach ($this->toArray() as $key => $value) {

			$group_keys = $group_by($value, $key);

			if (is_array($group_keys) === false) {
				$group_keys = [$group_keys];
			}

			foreach ($group_keys as $group_key) {
				$group_key = match (true) {
					is_bool($group_key) => (int)$group_key,
					$group_key instanceof Stringable => (string)$group_key,
					default => $group_key,
				};

				if ($results->missing($group_key)) {
					$results[$group_key] = new static();
				}

				$results[$group_key]->offsetSet($preserve_keys ? $key : null, $value);
			}
		}

		return $results;
	}

	public function keyBy(callable|string $value): static
	{
		$value = Internal::valueRetriever($value);
		$results = new static();

		foreach ($this->toArray() as $key => $item) {
			$results->set((string)$value($item, $key), $item);
		}

		return $results;
	}

	public function resetKeys(): static
	{
		$this->items = new Data(array_values($this->toArray()));
		return $this;
	}

	public function partition(callable $callback): static
	{
		$passed = new static();
		$failed = new static();

		foreach ($this->items as $key => $item) {
			if ($callback($item, $key)) {
				$passed->set($key, $item);
			} else {
				$failed->set($key, $item);
			}
		}

		return new static([$passed, $failed]);
	}

	public function chunk(int $size): static
	{
		$chunks = new static();
		foreach (array_chunk($this->toArray(), $size, true) as $chunk) {
			$chunks->append(new static($chunk));
		}
		return $chunks;
	}

	public function reduce(callable $callback, mixed $initial = null): mixed
	{
		return Arr::reduce($this->toArray(), $callback, $initial);
	}

	public function merge(mixed $data, bool $preserve = false): static
	{
		return $this->copy()->import($data, $preserve);
	}

	public function mergeIfMissing(array $data): static
	{
		foreach ($data as $key => $value) {
			if ($this->missing($key)) {
				$this->set($key, $value);
			}
		}
		return $this;
	}

	public function combine(mixed $values): static
	{
		return new static(array_combine($this->toArray(), Arr::from($values)));
	}

	public function flip(): static
	{
		$this->items = new Data(array_flip($this->toArray()));
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