<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use JsonSerializable;
use Rovota\Framework\Http\ApiError;
use Rovota\Framework\Http\Cookie\CookieObject;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Response\ResponseConfig;
use Rovota\Framework\Http\Response\ResponseManager;
use Rovota\Framework\Http\Response\ResponseObject;
use Rovota\Framework\Http\Response\Variants\ErrorResponseObject;
use Rovota\Framework\Http\Response\Variants\JsonResponseObject;
use Rovota\Framework\Http\Response\Variants\RedirectResponseObject;
use Rovota\Framework\Http\Response\Variants\StatusResponseObject;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Support\Facade;
use Throwable;

/**
 * @method static ResponseConfig config()
 *
 * @method static ResponseObject create(mixed $content, StatusCode|int $status = StatusCode::Ok)
 * @method static RedirectResponseObject redirect(UrlObject|string|null $location = null, StatusCode|int $status = StatusCode::Found)
 * @method static ErrorResponseObject error(Throwable|ApiError|array $error, StatusCode|int $status = StatusCode::Ok)
 * @method static JsonResponseObject json(JsonSerializable|array $content, StatusCode|int $status = StatusCode::Ok)
 * @method static StatusResponseObject status(StatusCode|int $content, StatusCode|int $status = StatusCode::Ok)
 *
 * @method static void attachHeader(string $name, string $value)
 * @method static void attachHeaders(array $headers)
 * @method static void withoutHeader(string $name)
 * @method static void withoutHeaders(array $names = [])
 *
 * @method static void attachCookie(CookieObject $cookie)
 * @method static void attachCookies(array $cookies)
 * @method static void withoutCookie(string $name)
 * @method static void withoutCookies()
 */
final class Response extends Facade
{

	public static function service(): ResponseManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return ResponseManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'config' => 'getConfig',

			'create' => 'createResponse',
			'redirect' => 'createRedirectResponse',
			'error' => 'createErrorResponse',
			'json' => 'createJsonResponse',
			'status' => 'createStatusResponse',

			default => $method,
		};
	}

}