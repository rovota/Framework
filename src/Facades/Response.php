<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use JsonSerializable;
use Rovota\Framework\Http\ApiError;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\ResponseManager;
use Rovota\Framework\Http\Responses\ErrorResponse;
use Rovota\Framework\Http\Responses\JsonResponse;
use Rovota\Framework\Http\Responses\RedirectResponse;
use Rovota\Framework\Routing\UrlObject;
use Throwable;

final class Response
{

	protected function __construct()
	{
	}

	// -----------------

	public static function make(mixed $content, StatusCode|int $status = StatusCode::Ok): \Rovota\Framework\Http\Response
	{
		return ResponseManager::createResponse($content, $status);
	}

	// -----------------

	public static function redirect(UrlObject|string|null $location = null, StatusCode|int $status = StatusCode::Found): RedirectResponse
	{
		return ResponseManager::createRedirectResponse($location, $status);
	}

	public static function error(Throwable|ApiError|array $error, StatusCode|int $status = StatusCode::Ok): ErrorResponse
	{
		return ResponseManager::createErrorResponse($error, $status);
	}

	public static function json(JsonSerializable|array $content, StatusCode|int $status = StatusCode::Ok): JsonResponse
	{
		return ResponseManager::createJsonResponse($content, $status);
	}

}