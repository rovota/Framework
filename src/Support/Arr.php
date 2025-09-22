<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Closure;
use Dflydev\DotAccessData\Data;
use JsonSerializable;
use Rovota\Framework\Support\Interfaces\Arrayable;

final class Arr
{

	protected function __construct()
	{
	}

	// -----------------

	public static function from(mixed $value): array
	{
		return match (true) {
			$value === null => [],
			is_array($value) => $value,
			$value instanceof Arrayable => $value->toArray(),
			$value instanceof JsonSerializable => Arr::from($value->jsonSerialize()),
			$value instanceof Data => $value->export(),
			default => [$value],
		};
	}

	// -----------------

	/**
	 * Returns the items from the array that pass a given truth test.
	 */
	public static function filter(array $array, callable $callback): array
	{
		$new = [];
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$new[$key] = Arr::filter($value, $callback);
				if (empty($new[$key])) {
					unset($new[$key]);
				}
			} else {
				if ($callback($value, $key) === true) {
					$new[$key] = $value;
				}
			}
		}

		return $new;
	}

	/**
	 * Returns the items from the array that pass a given truth test.
	 */
	public static function reject(array $array, callable $callback): array
	{
		$new = [];
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$new[$key] = Arr::reject($value, $callback);
				if (empty($new[$key])) {
					unset($new[$key]);
				}
			} else {
				if ($callback($value, $key) === false) {
					$new[$key] = $value;
				}
			}
		}

		return $new;
	}

	/**
	 * Returns the percentage of how often a given value appears in the given array.
	 */
	public static function percentage(array $array, mixed $value, int $precision = 2): float
	{
		if (empty($array)) return 0.00;

		if ($value instanceof Closure) {
			$count = count(Arr::filter($array, $value));
		} else {
			$count = count(Arr::filter($array, fn($data) => $data === $value));
		}

		return round($count / count($array) * 100, $precision);
	}

	// -----------------

	/**
	 * Returns the first item in the array, optionally the first that passes a given truth test.
	 */
	public static function first(array $array, callable|null $callback = null, mixed $default = null): mixed
	{
		if ($callback === null) {
			if (empty($array)) {
				return $default;
			}
			foreach ($array as $item) {
				return $item;
			}
		}

		return array_find($array, $callback);
	}

	/**
	 * Returns the last item in the array, optionally the last that passes a given truth test.
	 */
	public static function last(array $array, callable|null $callback = null, mixed $default = null): mixed
	{
		if ($callback === null) {
			return empty($array) ? $default : end($array);
		}

		return Arr::first(array_reverse($array, true), $callback, $default);
	}

	/**
	 * Returns one or more values from a given array. If amount is set to more than `1`, an array with values is returned.
	 */
	public static function random(array $array, int $amount = 1): mixed
	{
		$count = count($array);
		$requested = $amount === 0 ? 1 : (($amount > $count) ? $count : $amount);

		if ($requested === 1) {
			return $array[array_rand($array)];
		}

		$keys = array_rand($array, $requested);
		$result = [];

		foreach ($keys as $key) {
			$result[$key] = $array[$key];
		}

		return $result;
	}

	// -----------------

	/**
	 * Collapse an array or collection of arrays into a single, flat array.
	 */
	public static function collapse(array $array): array
	{
		$normalized = [];

		foreach ($array as $item) {
			if ($item instanceof Arrayable) {
				$item = $item->toArray();
			} else {
				if (!is_array($item)) {
					continue;
				}
			}
			$normalized[] = $item;
		}

		return array_merge([], ...$normalized);
	}

	/**
	 * Reduces the array to a single value, passing the result of each iteration into the next:
	 */
	public static function reduce(array $array, callable $callback, mixed $initial = null): mixed
	{
		$result = $initial;
		foreach ($array as $key => $value) {
			$result = $callback($result, $value, $key);
		}
		return $result;
	}

	public static function map(array $array, callable $callback): array
	{
		$keys = array_keys($array);
		$items = array_map($callback, $array, $keys);

		return array_combine($keys, $items);
	}

	public static function mode(array $array): array|null
	{
		if (count($array) === 0) {
			return null;
		}

		$appearances = [];
		foreach ($array as $item) {
			if (!isset($appearances[$item])) {
				$appearances[$item] = 0;
			}
			$appearances[$item]++;
		}

		$modes = array_keys($appearances, max($appearances));
		sort($modes);
		return $modes;
	}

	/**
	 * Returns a new array with each entry only containing the specified field, optionally keyed by the given key.
	 */
	public static function pluck(array $array, string $field, string|null $key = null): array
	{
		$results = [];
		$fields = explode('.', $field);
		$key = is_string($key) ? explode('.', $key) : $key;

		foreach ($array as $item) {
			$item_value = Internal::getData($item, $fields);

			if ($item_value !== null) {
				if ($key === null) {
					$results[] = $item_value;
				} else {
					$item_key = Internal::getData($item, $key);
					if (is_object($item_key) && method_exists($item_key, '__toString')) {
						$item_key = (string)$item_key;
					}
					$results[$item_key] = $item_value;
				}
			}
		}

		return $results;
	}

	// -----------------

	public static function has(array $array, mixed $key): bool
	{
		return array_all(is_array($key) ? $key : [$key], fn($key) => array_key_exists($key, $array) === true);
	}

	public static function missing(array $array, mixed $key): bool
	{
		return array_all(is_array($key) ? $key : [$key], fn($key) => array_key_exists($key, $array) === false);
	}

	public static function contains(array $haystack, mixed $needle): bool
	{
		$needles = is_array($needle) ? $needle : [$needle];

		foreach ($needles as $needle) {
			if ($needle instanceof Closure) {
				if (array_any($haystack, fn($value, $key) => $needle($value, $key) === false)) {
					return false;
				}
			} else {
				if (in_array($needle, $haystack, true) === false) {
					return false;
				}
			}
		}

		return true;
	}

	public static function containsAny(array $haystack, array $needles): bool
	{
		foreach ($needles as $needle) {
			if ($needle instanceof Closure) {
				if (array_any($haystack, fn($value, $key) => $needle($value, $key))) {
					return true;
				}
			} else {
				if (in_array($needle, $haystack, true)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Returns the corresponding key of the searched value when found. Uses strict comparisons by default.
	 * Optionally, you can pass a closure to search for the first item that matches a truth test.
	 */
	public static function search(array $array, mixed $value, bool $strict = true): string|int|bool
	{
		if ($value instanceof Closure === false) {
			if (is_object($value)) {
				$value = spl_object_hash($value);
			}
			return array_search($value, $array, $strict);
		}

		$callable = $value;
		foreach ($array as $key => $value) {
			if (is_object($value)) {
				$value = spl_object_hash($value);
			}
			if ($callable($value, $key)) {
				return $key;
			}
		}

		return false;
	}

	// -----------------

	public static function sort(array $array, callable|null $callback = null, bool $descending = false): array
	{
		if (is_callable($callback)) {
			uasort($array, $callback);
		} else {
			$descending ? arsort($array) : asort($array);
		}
		return $array;
	}

	public static function sortBy(array $array, mixed $callback, bool $descending = false): array
	{
		$results = [];
		$callback = Internal::valueRetriever($callback);

		foreach ($array as $key => $value) {
			$results[$key] = $callback($value, $key);
		}

		$descending ? arsort($results) : asort($results);

		foreach (array_keys($results) as $key) {
			$results[$key] = $array[$key];
		}

		return $results;
	}

	public static function sortKeys(array $array, bool $descending = false): array
	{
		$descending ? krsort($array) : ksort($array);
		return $array;
	}

	public static function shuffle(array $array): array
	{
		shuffle($array);
		return $array;
	}

}