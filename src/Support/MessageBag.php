<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use Dflydev\DotAccessData\Data;
use Dflydev\DotAccessData\DataInterface;
use IteratorAggregate;
use JsonSerializable;
use Rovota\Framework\Support\Interfaces\Arrayable;
use Traversable;

class MessageBag implements ArrayAccess, IteratorAggregate, Countable, Arrayable, JsonSerializable
{

	protected Data $items;

	// -----------------

	public function __construct(mixed $items = [])
	{
		$this->items = new Data(Arr::from($items));
	}

	// -----------------

	public function formatted(string|null $key = null): array
	{
		$result = [];

		foreach ($this->toArray() as $type => $items) {
			if ($key !== null && $key !== $type) {
				continue;
			}

			foreach ($items as $identifier => $error) {
				$result[$type][$identifier] = $error->formatted();
			}
		}

		return ($key !== null ? $result[$key] ?? [] : $result) ?? [];
	}

	// -----------------

	public function copy(): static
	{
		return clone $this;
	}

	public function count(mixed $key = null): int
	{
		return count($key !== null ? ($this->get($key) ?? []) : $this->items->export());
	}

	public function flush(): static
	{
		$this->items = new Data();
		return $this;
	}

	public function get(mixed $key, mixed $default = null): mixed
	{
		return $this->items->get($key, ($default instanceof Closure ? $default() : $default));
	}

	public function has(mixed $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		return array_all($keys, fn($key) => $this->offsetExists($key) !== false);
	}

	public function import(mixed $data, bool $preserve = false): static
	{
		$mode = $preserve ? DataInterface::PRESERVE : DataInterface::MERGE;
		$this->items->import(Arr::from($data), $mode);
		return $this;
	}

	public function isEmpty(): bool
	{
		return empty($this->items->export());
	}

	public function missing(mixed $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		return array_all($keys, fn($key) => $this->offsetExists($key) !== true);
	}

	public function only(array $keys): static
	{
		$bucket = new static();
		foreach ($keys as $key) {
			$bucket->set($key, $this->items->get($key));
		}
		return $bucket;
	}

	public function remove(mixed $key): static
	{
		if (is_array($key)) {
			foreach ($key as $offset) {
				$this->remove($offset);
			}
		} else {
			$this->offsetUnset($key);
		}
		return $this;
	}

	public function set(mixed $key, Message|string $value, array $data = []): static
	{
		if (is_string($value)) {
			$value = new Message(Str::afterLast($key, '.'), $value, $data);
		}

		$this->offsetSet($key, $value);

		return $this;
	}

	public function toArray(): array
	{
		return $this->items->export();
	}

	// -----------------

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
	public function jsonSerialize(): array
	{
		return $this->toArray();
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
		return $this->items->get($offset);
	}

	/**
	 * @internal
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		if ($offset === null) {
			$items = $this->items->export();
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