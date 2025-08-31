<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Structures\Traits;

use Dflydev\DotAccessData\Data;
use Rovota\Framework\Support\Arr;

trait ValueModifiers
{

	public function increment(mixed $key, int $step = 1): static
	{
		$this->set($key, (int)$this->get($key, 0) + abs($step));
		return $this;
	}

	public function decrement(mixed $key, int $step = 1): static
	{
		$this->set($key, (int)$this->get($key, 0) - abs($step));
		return $this;
	}

	// -----------------

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

}