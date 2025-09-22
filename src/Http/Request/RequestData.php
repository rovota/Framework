<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Request;

use Rovota\Framework\Structures\Bucket;

class RequestData extends Bucket
{

	public function filled(mixed $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		return array_all($keys, fn($key) => $this->items->get($key, null) !== null);
	}

	// -----------------

	public function whenFilled(string $key, callable $callback): mixed
	{
		if ($this->filled($key)) {
			return $callback($this->items->get($key));
		}
		return null;
	}

	public function whenPresent(string $key, callable $callback): mixed
	{
		if ($this->has($key)) {
			return $callback($this->items->get($key));
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

}