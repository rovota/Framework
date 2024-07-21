<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

use Dflydev\DotAccessData\Data;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Request;
use Rovota\Framework\Http\RequestObject;
use Rovota\Framework\Kernel\Application;
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
// Components

if (!function_exists('request')) {
	function request(): RequestObject
	{
		return Request::current();
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
// Data Conversion

if (!function_exists('json_encode_clean')) {
	function json_encode_clean(mixed $value, int $depth = 512): false|string
	{
		return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK, $depth);
	}
}

if (!function_exists('convert_to_array')) {
	function convert_to_array(mixed $value): array
	{
		return match(true) {
			$value === null => [],
			is_array($value) => $value,
			$value instanceof Arrayable => $value->toArray(),
			$value instanceof JsonSerializable => convert_to_array($value->jsonSerialize()),
			$value instanceof Data => $value->export(),
			default => [$value],
		};
	}
}

// -----------------
// Misc

if (!function_exists('quit')) {
	function quit(StatusCode $code = StatusCode::InternalServerError): never
	{
		Application::quit($code);
	}
}

if (!function_exists('dump')) {
	function dump(mixed $data, bool $exit = false): void
	{
		print_r($data);
		if ($exit) {
			quit();
		};
	}
}

if (!function_exists('deprecated')) {
	function deprecated(string $message): void
	{
		trigger_error($message, E_USER_DEPRECATED);
	}
}