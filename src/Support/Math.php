<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

final class Math
{

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * Determines whether two floating values are equal, accounting for rounding precision.
	 */
	public static function floatEquals(float $first, float $second, int $precision = 14): bool
	{
		return round($first, $precision) === round($second, $precision);
	}

	// -----------------

	public static function isEven(float|int $value): bool
	{
		return (int) $value % 2 === 0;
	}

	public static function isOdd(float|int $value): bool
	{
		return (int) $value & 1;
	}

	// -----------------

	/**
	 * Returns the lowest value present in the given list of values.
	 */
	public static function min(array $values, float|int|null $limit = null): float|int
	{
		$minimum = min($values);
		return ($limit !== null && $minimum <= $limit) ? $limit : $minimum;
	}

	/**
	 * Returns the highest value present in the given list of values.
	 */
	public static function max(array $values, float|int|null $limit = null): float|int
	{
		$maximum = max($values);
		return ($limit !== null && $maximum >= $limit) ? $limit : $maximum;
	}

	/**
	 * Returns the median of a given array. When the list is empty or contains non-numeric values, `0` will be returned. Optionally, the values to sum can be filtered.
	 */
	public static function median(array $values, int $precision = 14, callable|null $filter = null): float|int
	{
		$values = $filter === null ? $values : Arr::filter($values, $filter);
		$count = count($values);

		if ($count === 0) {
			return 0;
		}

		sort($values);

		$values = array_values($values);
		$middle = (int) ($count / 2);

		if ($count % 2) {
			return round($values[$middle], $precision);
		}

		return Math::average([$values[$middle - 1], $values[$middle]], $precision);
	}

	/**
	 * Returns the average of the given values. When the list is empty or contains non-numeric values, `0` will be returned. Optionally, the values to sum can be filtered.
	 */
	public static function average(array $values, int $precision = 14, callable|null $filter = null): float|int
	{
		$values = $filter === null ? $values : Arr::filter($values, $filter);
		$count = count($values);

		if ($count < 2) {
			return array_pop($values) ?? 0;
		}

		$result = array_sum($values) / $count;
		return $precision === 0 ? $result : round($result, $precision);
	}

	/**
	 * Returns the sum of all items in the list. Optionally, the values to sum can be filtered.
	 */
	public static function sum(array $values, int $precision = 14, callable|null $filter = null): int|float
	{
		$result = array_sum($filter === null ? $values : Arr::filter($values, $filter));
		return $precision === 0 ? $result : round($result, $precision);
	}

	/**
	 * Returns the range of all items in the list. Optionally, the values to sum can be filtered.
	 */
	public static function range(array $values, int $precision = 14, callable|null $filter = null): int|float
	{
		$result = $filter === null ? $values : Arr::filter($values, $filter);
		$result = max($result) - min($result);
		return $precision === 0 ? $result : round($result, $precision);
	}

}