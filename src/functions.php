<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

use Dflydev\DotAccessData\Data;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\RequestManager;
use Rovota\Framework\Http\RequestObject;
use Rovota\Framework\Http\ResponseManager;
use Rovota\Framework\Http\ResponseObject;
use Rovota\Framework\Http\Responses\RedirectResponseObject;
use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Support\Interfaces\Arrayable;
use Rovota\Framework\Support\Moment;
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

if (!function_exists('__')) {
	function __(string|null $string, array|object $data = []): string
	{
		return Str::translate($string, $data);
	}
}

// -----------------
// DateTime

if (!function_exists('now')) {
	function now(DateTimeZone|string|int|null $timezone = null): Moment
	{
		return new Moment(timezone: $timezone);
	}
}

if (!function_exists('moment')) {
	function moment(mixed $time = 'now', DateTimeZone|string|int|null $timezone = null): Moment|null
	{
		return new Moment($time, $timezone);
	}
}

// -----------------
// Components

if (!function_exists('request')) {
	function request(): RequestObject
	{
		return RequestManager::instance()->getCurrent();
	}
}

if (!function_exists('response')) {
	function response(mixed $content, StatusCode|int $status = StatusCode::Ok): ResponseObject
	{
		return ResponseManager::instance()->createResponse($content, $status);
	}
}

if (!function_exists('redirect')) {
	function redirect(UrlObject|string|null $location = null, StatusCode|int $status = StatusCode::Found): RedirectResponseObject
	{
		return ResponseManager::instance()->createRedirectResponse($location, $status);
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
		Framework::quit($code);
	}
}

if (!function_exists('dump')) {
	function dump(mixed $data, bool $exit = false): void
	{
		print_r($data);
		if ($exit) {
			quit();
		}
	}
}

if (!function_exists('deprecated')) {
	function deprecated(string $message): void
	{
		trigger_error($message, E_USER_DEPRECATED);
	}
}

if (!function_exists('limit')) {
	function limit(int|float $value, int|float $minimum, int|float $maximum): int|float
	{
		return min(max($minimum, $value), $maximum);
	}
}