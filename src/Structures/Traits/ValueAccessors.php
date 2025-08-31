<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Structures\Traits;

use Closure;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Internal;
use Rovota\Framework\Support\Math;

trait ValueAccessors
{

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

	public function first(callable|null $callback = null, mixed $default = null): mixed
	{
		return Arr::first($this->toArray(), $callback, $default);
	}

	public function last(callable|null $callback = null, mixed $default = null): mixed
	{
		return Arr::last($this->toArray(), $callback, $default);
	}

	public function random(int $amount = 1): static
	{
		$result = Arr::random($this->toArray(), $amount);
		return new static(is_array($result) ? $result : [$result]);
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
	 * Only returns items that match a given truth test.
	 */
	public function reject(callable $callback): static
	{
		return new static(Arr::reject($this->toArray(), $callback));
	}

	/**
	 * Only returns the items that are specified by given keys.
	 */
	public function only(array $keys): static
	{
		$bucket = new static();
		foreach ($keys as $key) {
			$bucket->set($key, $this->offsetGet($key));
		}
		return $bucket;
	}

	/**
	 * Returns all items, except for those with the key(s) given.
	 */
	public function except(array $keys): static
	{
		return $this->copy()->remove($keys);
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

	/**
	 * Returns all items, but only containing the fields specified.
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

}