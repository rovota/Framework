<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Request;

class RequestHeaders extends RequestData
{

	public function import(mixed $data, bool $preserve = false): static
	{
		return parent::import(array_change_key_case($data), $preserve);
	}

	// -----------------

	/**
	 * @internal
	 */
	public function offsetExists(mixed $offset): bool
	{
		return parent::offsetExists(strtolower($offset));
	}

	/**
	 * @internal
	 */
	public function offsetGet(mixed $offset): mixed
	{
		return parent::offsetGet(strtolower($offset));
	}

	/**
	 * @internal
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		parent::offsetSet(strtolower($offset), $value);
	}

	/**
	 * @internal
	 */
	public function offsetUnset(mixed $offset): void
	{
		parent::offsetUnset(strtolower($offset));
	}

}