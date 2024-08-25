<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use ArrayAccess;
use Rovota\Framework\Structures\Sequence;
use Rovota\Framework\Support\Interfaces\Arrayable;

/**
 * @internal
 */
final class Internal
{

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * Makes it easier to retrieve values from objects and other data. Given value is passed to the returned value.
	 *
	 * Inspired by the Laravel `value_retriever()` function.
	 */
	public static function valueRetriever(mixed $value): callable
	{
		if (!is_string($value) && is_callable($value)) {
			return $value;
		}

		return function ($item) use ($value) {
			return self::getData($item, $value);
		};
	}

	// -----------------

	/**
	 * Attempts to retrieve data for a given key on a provided target. Optionally, a default value can be provided if no data can be found.
	 *
	 * Inspired by the Laravel `data_get()` function.
	 */
	public static function getData(mixed $target, string|array|null $key, mixed $default = null): mixed
	{
		if ($key === null) {
			return $target;
		}

		$key = is_array($key) ? $key : explode('.', $key);


		foreach ($key as $i => $segment) {

			unset($key[$i]);

			if ($segment === null) {
				return $target;
			}

			if ($segment === '*') {
				if ($target instanceof Arrayable) {
					$target = $target->toArray();
				} elseif (!is_iterable($target)) {
					return $default;
				}

				$result = [];
				foreach ($target as $item) {
					$result[] = self::getData($item, $key);
				}

				return in_array('*', $key) ? Arr::collapse($result) : $result;
			}

			$target = match (true) {
				$target instanceof Sequence => is_int($segment) ? $target->offsetGet($segment) : 1,
				$target instanceof ArrayAccess => $target->offsetGet($segment),
				is_object($target) && isset($target->{$segment}) => $target->{$segment},
				is_object($target) && method_exists($target, $segment) => $target->{$segment}(),
				is_array($target) && array_key_exists($segment, $target) => $target[$segment],
				default => null,
			};

			if ($target === null) {
				return $default;
			}
		}

		return $target;
	}

}