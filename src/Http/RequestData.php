<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use Rovota\Framework\Structures\Bucket;

class RequestData extends Bucket
{

	public function filled(mixed $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		foreach ($keys as $key) {
			if ($this->items->get($key, null) === null) {
				return false;
			}
		}
		return true;
	}

	// -----------------

	public function whenFilled(string $key, callable $callback): mixed
	{
		if ($this->filled($key)) {
			return $callback();
		}
		return null;
	}

	public function whenPresent(string $key, callable $callback): mixed
	{
		if ($this->has($key)) {
			return $callback();
		}
		return null;
	}

	public function whenMissing(string $key, callable $callback): mixed
	{
		if ($this->has($key) === false) {
			return $callback();
		}
		return null;
	}

	// -----------------

	/**
	 * @internal
	 */
	public function offsetExists(mixed $offset): bool
	{
		return $this->items->has($offset);
	}

	/**
	 * @internal
	 */
	public function offsetGet(mixed $offset): mixed
	{
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
		$this->items->set($offset, $value);
	}

	/**
	 * @internal
	 */
	public function offsetUnset(mixed $offset): void
	{
		$this->items->remove($offset);
	}

}