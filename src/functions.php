<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

use Dflydev\DotAccessData\Data;
use Rovota\Framework\Auth\AuthManager;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Http\Request\RequestObject;
use Rovota\Framework\Http\Response\DefaultResponse;
use Rovota\Framework\Http\Response\Extensions\RedirectResponse;
use Rovota\Framework\Http\Response\ResponseManager;
use Rovota\Framework\Identity\Models\User;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Security\CsrfManager;
use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Storage\StorageManager;
use Rovota\Framework\Support\Interfaces\Arrayable;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Text;
use Rovota\Framework\Support\Url;
use Rovota\Framework\Views\Interfaces\ViewInterface;
use Rovota\Framework\Views\ViewManager;
use function DeepCopy\deep_copy;

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
		return new Moment('now', $timezone);
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
		return RequestManager::instance()->current();
	}
}

if (!function_exists('response')) {
	function response(mixed $content, StatusCode|int $status = StatusCode::Ok): DefaultResponse
	{
		return ResponseManager::instance()->createResponse($content, $status);
	}
}

if (!function_exists('redirect')) {
	function redirect(UrlObject|string|null $location = null, StatusCode|int $status = StatusCode::Found): RedirectResponse
	{
		return ResponseManager::instance()->createRedirectResponse($location, $status);
	}
}

if (!function_exists('view')) {
	function view(string $template, string|null $class = null): ViewInterface
	{
		return ViewManager::instance()->createView($template, $class);
	}
}

if (!function_exists('user')) {
	function user(): User|null
	{
		return AuthManager::instance()->get()?->user();
	}
}

// -----------------

if (!function_exists('route')) {
	function route(string $name, array $context = [], array $parameters = []): UrlObject
	{
		return Url::route($name, $context, $parameters);
	}
}

if (!function_exists('file')) {
	function file(string $location, string|null $disk = null): File|null
	{
		return StorageManager::instance()->get($disk)->file($location);
	}
}

// -----------------
// Security

if (!function_exists('csrf_token')) {
	function csrf_token(): string
	{
		return CsrfManager::getToken();
	}
}

if (!function_exists('csrf_token_name')) {
	function csrf_token_name(): string
	{
		return CsrfManager::getTokenName();
	}
}

if (!function_exists('form_submit_time')) {
	function form_submit_time(): float
	{
		if (request()->missing('submit_timestamp')) {
			return 0.0;
		}
		return microtime(true) - request()->float('submit_timestamp');
	}
}

if (!function_exists('form_submit_time_allowed')) {
	function form_submit_time_allowed(): bool
	{
		$submit_time = form_submit_time();
		$submit_time_min = Registry::float('security.form.submit_time_min', 0.3);
		$submit_time_max = Registry::float('security.form.submit_time_max', 1800);

		return $submit_time > $submit_time_min && $submit_time < $submit_time_max;
	}
}

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

if (!function_exists('deep_clone')) {
	function deep_clone(mixed $instance): mixed
	{
		return deep_copy($instance);
	}
}

if (!function_exists('throw_if')) {
	/**
	 * @throws Throwable
	 */
	function throw_if(bool $bool, Throwable|string $throwable, string|null $message = ''): void
	{
		if ($bool === true) {
			throw (is_string($throwable) ? new $throwable($message) : $throwable);
		}
	}
}

if (!function_exists('throw_unless')) {
	/**
	 * @throws Throwable
	 */
	function throw_unless(bool $bool, Throwable|string $throwable, string|null $message = ''): void
	{
		if ($bool === false) {
			throw (is_string($throwable) ? new $throwable($message) : $throwable);
		}
	}
}

if (!function_exists('retry')) {
	function retry(int $attempts, callable $action, callable|int $delay = 100, callable|null $filter = null, mixed $fallback = null): mixed
	{
		$throwable = null;
		$value = null;

		for ($tries = 1; $tries < $attempts + 1; $tries++) {
			try {
				$value = $action();
			} catch (Throwable $e) {
				if ($filter === null || (is_callable($filter) && $filter($e))) {
					if ($tries === $attempts) {
						$throwable = $e;
					}
					$delay = is_callable($delay) ? $delay($tries) : $delay;
					usleep($delay * 1000);
					continue;
				}
			}
			break;
		}

		if ($throwable instanceof Throwable) {
			ExceptionHandler::logThrowable($throwable);
			return $fallback;
		} else {
			return $value;
		}
	}
}