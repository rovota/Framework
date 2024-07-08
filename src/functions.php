<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

use Rovota\Framework\Support\Interfaces\Arrayable;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Text;

// -----------------
// Strings

if (!function_exists('text')) {
	function text(string $string): Text
	{
		return new Text($string);
	}
}

if (!function_exists('e')) {
	function e(string|null $string): string|null
	{
		return Str::escape($string);
	}
}

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------
// Internal

if (!function_exists('source')) {
	function source(string $path = ''): string
	{
		return local($path, dirname(__FILE__));
	}
}

if (!function_exists('local')) {
	function local(string $path = '', string|null $base = null): string
	{
		$base = $base ?? (defined('BASE_PATH') ? BASE_PATH : dirname(__FILE__));
		return strlen($path) > 0 ? $base.'/'.ltrim($path, '/') : $base;
	}
}

if (!function_exists('value_retriever')) {
	function value_retriever(mixed $value): callable
	{
		// Inspired by the Laravel valueRetriever() function.
		if (!is_string($value) && is_callable($value)) {
			return $value;
		}

		return function ($item) use ($value) {
			return data_get($item, $value);
		};
	}
}

if (!function_exists('data_get')) {
	function data_get(mixed $target, string|array|null $key, mixed $default = null): mixed
	{
		// Inspired by the Laravel data_get() function.
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
					$result[] = data_get($item, $key);
				}

				return in_array('*', $key) ? Arr::collapse($result) : $result;
			}

			$target = match (true) {
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