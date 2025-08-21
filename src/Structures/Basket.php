<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Structures;

use Closure;
use Dflydev\DotAccessData\Data;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Interfaces\Arrayable;
use Rovota\Framework\Support\Internal;
use Rovota\Framework\Support\Math;
use Rovota\Framework\Support\Traits\Conditionable;
use Rovota\Framework\Support\Traits\Macroable;
use Stringable;

class Basket extends Bucket
{
	use Conditionable, Macroable;

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

	// -----------------

	public function min(string|null $field = null, float|int|null $limit = null): float|int
	{
		$data = $field !== null ? $this->pluck($field)->toArray() : $this->toArray();
		return Math::min($data, $limit);
	}

	public function max(string|null $field = null, float|int|null $limit = null): float|int
	{
		$data = $field !== null ? $this->pluck($field)->toArray() : $this->toArray();
		return Math::max($data, $limit);
	}

	public function average(string|null $field = null, int $precision = 14, callable|null $filter = null): float|int
	{
		$data = $field !== null ? $this->pluck($field)->toArray() : $this->toArray();
		return Math::average($data, $precision, $filter);
	}

	public function sum(string|null $field = null, int $precision = 14, callable|null $filter = null): float|int
	{
		$data = $field !== null ? $this->pluck($field)->toArray() : $this->toArray();
		return Math::sum($data, $precision, $filter);
	}

	public function range(string|null $field = null, int $precision = 14, callable|null $filter = null): int|float
	{
		$values = $field !== null ? $this->pluck($field)->toArray() : $this->toArray();
		return Math::range($values, $precision, $filter);
	}

	// -----------------

	public function random(int $amount = 1): static
	{
		$result = Arr::random($this->toArray(), $amount);
		return new static(is_array($result) ? $result : [$result]);
	}

	// -----------------

	public function concat(mixed $data): static
	{
		foreach (Arr::from($data) as $item) {
			$this->append($item);
		}
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

	/**
	 * Count how often a given value appears.
	 */
	public function occurrences(string $value): int
	{
		return $this->countBy()[$value] ?? 0;
	}

	/**
	 * Return all values that appear more than once. Keys are preserved.
	 */
	public function duplicates(callable|string|null $callback = null): static
	{
		$items = $this->map(Internal::valueRetriever($callback));
		$duplicates = new static();
		$counters = [];

		foreach ($items as $key => $value) {
			if (isset($counters[$value]) === false) {
				$counters[$value] = 1;
				continue;
			}
			$duplicates->set($key, $value);
		}

		return $duplicates;
	}

	// -----------------

	/**
	 * Return all items, but only containing the fields specified.
	 */
	public function fields(array $fields): static
	{
		$result = new static();

		foreach ($this->toArray() as $key => $item) {
			$entry = [];
			foreach ($fields as $field) {
				$entry[$field] = Internal::getData($item, $field);
			}
			$result->set($key, $entry);
		}

		return $result;
	}

	/**
	 * Returns a new bucket with each entry only containing the specified field, optionally keyed by the given key.
	 */
	public function pluck(string $field, string|null $key = null): static
	{
		return new static(Arr::pluck($this->toArray(), $field, $key));
	}

	/**
	 * Return the first item(s) in the bucket and remove it/them.
	 */
	public function shift(int $count = 1): mixed
	{
		if ($this->isEmpty()) {
			return null;
		}

		if ($count === 1) {
			$value = $this->first();
			$this->remove(array_search($value, $this->toArray()));
			return $value;
		}

		$results = [];
		$item_count = $this->count();

		foreach (range(1, min($count, $item_count)) as $ignored) {
			$results[] = $this->first();
			$this->shift();
		}

		return new static($results);
	}

	/**
	 * Return the last item(s) in the bucket and remove it/them.
	 */
	public function pop(int $count = 1): mixed
	{
		if ($this->isEmpty()) {
			return null;
		}

		if ($count === 1) {
			$value = $this->last();
			$this->remove(array_search($value, $this->toArray()));
			return $value;
		}

		$results = [];
		$item_count = $this->count();

		foreach (range(1, min($count, $item_count)) as $ignored) {
			$results[] = $this->last();
			$this->pop();
		}

		return new static($results);
	}

	/**
	 * Return a slice starting at the given offset (key), with a maximum of a given length.
	 */
	public function slice(int $offset, int|null $length = null): static
	{
		return new static(array_slice($this->toArray(), $offset, $length, true));
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

	/**
	 * Iterates over all items, executing the given callback while passing its key and value.
	 *
	 * **Creates a new Bucket with the updated values.**
	 */
	public function map(callable $callback): static
	{
		return new static(Arr::map($this->toArray(), $callback));
	}

	/**
	 * Iterates over all items, executing the given callback while passing its key and value.
	 *
	 * **Modifies the values in the current Bucket.**
	 */
	public function transform(callable $callback): static
	{
		$this->items = new Data(Arr::map($this->toArray(), $callback));
		return $this;
	}

	/**
	 * Iterates over all items, executing the given callback while passing its key and value. To stop iterating, return `false` from the callback.
	 *
	 * **Returned values other than** `false` **are ignored, and the Bucket data stays untouched.**
	 */
	public function each(callable $callback): static
	{
		foreach ($this->toArray() as $key => $value) {
			if ($callback($value, $key) === false) {
				break;
			}
		}
		return $this;
	}

	public function replace(mixed $replacements): static
	{
		$this->items = new Data(array_replace($this->toArray(), Arr::from($replacements)));
		return $this;
	}

	public function replaceRecursive(mixed $replacements): static
	{
		$this->items = new Data(array_replace_recursive($this->toArray(), Arr::from($replacements)));
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

	public function intersect(mixed $items): static
	{
		$this->items = new Data(array_intersect($this->toArray(), Arr::from($items)));
		return $this;
	}

	public function intersectByKeys(mixed $items): static
	{
		$this->items = new Data(array_intersect_key($this->toArray(), Arr::from($items)));
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

	public function merge(mixed $with, bool $preserve = false): static
	{
		return $this->copy()->import($with, $preserve);
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

	public function take(int $count): static
	{
		if ($count < 0) {
			return $this->slice($count, abs($count));
		}

		return $this->slice(0, $count);
	}

	public function takeFrom(mixed $closure): static
	{
		$result = new static();
		$found = false;

		foreach ($this->toArray() as $key => $value) {
			if ($found === true) {
				$result->set($key, $value);
				continue;
			}

			if (($closure instanceof Closure && $closure($value, $key)) || $value === $closure) {
				$result->set($key, $value);
				$found = true;
			}
		}
		return $result;
	}

	public function takeUntil(mixed $closure): static
	{
		$result = new static();

		foreach ($this->toArray() as $key => $value) {
			if (($closure instanceof Closure && $closure($value, $key)) || $value === $closure) {
				break;
			}
			$result->set($key, $value);
		}
		return $result;
	}

	// -----------------

	public function diff(mixed $items): static
	{
		return new static(array_diff($this->toArray(), Arr::from($items)));
	}

	public function diffAssoc(mixed $items): static
	{
		return new static(array_diff_assoc($this->toArray(), Arr::from($items)));
	}

	public function diffKeys(mixed $items): static
	{
		return new static(array_diff_key($this->toArray(), Arr::from($items)));
	}

}