<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use JsonSerializable;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Responses\ErrorResponse;
use Rovota\Framework\Http\Responses\JsonResponse;
use Rovota\Framework\Http\Responses\RedirectResponse;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Structures\Config;
use Rovota\Framework\Support\Internal;
use Throwable;

/**
 * @internal
 */
final class ResponseManager
{

	protected static Config $config;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		$config = require Internal::projectFile('config/responses.php');

		self::$config = new Config($config);

		self::$config->set([
			'headers.X-Powered-By' => 'Rovota Framework',
			'headers.X-XSS-Protection' => '0',
		]);
	}

	// -----------------

	public static function getConfig(): Config
	{
		return self::$config;
	}

	// -----------------

	public static function createResponse(mixed $content, StatusCode|int $status = StatusCode::Ok): Response
	{
		// TODO: Return different response classes based on detected content.

		// FileResponse

		// ImageResponse

		// RedirectResponse
		if ($content instanceof UrlObject) {
			return self::createRedirectResponse($content, $status);
		}

		// ErrorResponse
		if ($content instanceof Throwable || $content instanceof ApiError) {
			return self::createErrorResponse($content, $status);
		}

		// JsonResponse
		if ($content instanceof JsonSerializable || is_array($content)) {
			return self::createJsonResponse($content, $status);
		}

		// ViewResponse

		return new Response($content, $status, self::$config);
	}

	// -----------------

	public static function createRedirectResponse(UrlObject|string|null $location = null, StatusCode|int $status = StatusCode::Found): RedirectResponse
	{
		return new RedirectResponse($location, $status, self::$config);
	}

	public static function createErrorResponse(Throwable|ApiError|array $error, StatusCode|int $status = StatusCode::Ok): ErrorResponse
	{
		return new ErrorResponse($error, $status, self::$config);
	}

	public static function createJsonResponse(JsonSerializable|array $content, StatusCode|int $status = StatusCode::Ok): JsonResponse
	{
		return new JsonResponse($content, $status, self::$config);
	}

}