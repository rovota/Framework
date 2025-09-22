<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use JsonSerializable;
use Rovota\Framework\Http\Cookie\CookieObject;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Errors\Error;
use Rovota\Framework\Http\Response\DefaultResponse;
use Rovota\Framework\Http\Response\Extensions\ErrorResponse;
use Rovota\Framework\Http\Response\Extensions\FileResponse;
use Rovota\Framework\Http\Response\Extensions\JsonResponse;
use Rovota\Framework\Http\Response\Extensions\RedirectResponse;
use Rovota\Framework\Http\Response\Extensions\StatusResponse;
use Rovota\Framework\Http\Response\ResponseManager;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Support\Facade;
use Rovota\Framework\Views\View;
use Throwable;

/**
 * @method static DefaultResponse create(mixed $content, StatusCode|int $status = StatusCode::Ok)
 * @method static FileResponse file(File $content, StatusCode|int $status = StatusCode::Found)
 * @method static RedirectResponse redirect(UrlObject|string|null $location = null, StatusCode|int $status = StatusCode::Found)
 * @method static ErrorResponse error(Throwable|Error|array $error, StatusCode|int $status = StatusCode::Ok)
 * @method static JsonResponse json(JsonSerializable|array $content, StatusCode|int $status = StatusCode::Ok)
 * @method static View view(View $content, StatusCode|int $status = StatusCode::Ok)
 * @method static StatusResponse status(StatusCode|int $content, StatusCode|int $status = StatusCode::Ok)
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
			'create' => 'createResponse',
			'redirect' => 'createRedirectResponse',
			'error' => 'createErrorResponse',
			'json' => 'createJsonResponse',
			'view' => 'createViewResponse',
			'status' => 'createStatusResponse',

			default => $method,
		};
	}

}