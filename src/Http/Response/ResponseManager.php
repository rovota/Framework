<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Response;

use JsonSerializable;
use Rovota\Framework\Http\Error;
use Rovota\Framework\Http\Cookie\CookieObject;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Response\Extensions\ErrorResponse;
use Rovota\Framework\Http\Response\Extensions\FileResponse;
use Rovota\Framework\Http\Response\Extensions\JsonResponse;
use Rovota\Framework\Http\Response\Extensions\RedirectResponse;
use Rovota\Framework\Http\Response\Extensions\StatusResponse;
use Rovota\Framework\Http\Response\Extensions\ViewResponse;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Views\View;
use Throwable;

/**
 * @internal
 */
final class ResponseManager extends ServiceProvider
{

	protected Config $config;

	// -----------------

	public function __construct()
	{
		$this->config = ResponseConfig::load('config/responses');

		$this->config->set([
			'headers.X-Powered-By' => 'Rovota Framework',
			'headers.X-XSS-Protection' => '0',
		]);
	}

	// -----------------

	public function createResponse(mixed $content, StatusCode|int $status = StatusCode::Ok): DefaultResponse
	{
		// FileResponse
		if ($content instanceof File) {
			return self::createFileResponse($content, $status);
		}

		// RedirectResponse
		if ($content instanceof UrlObject) {
			return self::createRedirectResponse($content, $status);
		}

		// ErrorResponse
		if ($content instanceof Throwable || $content instanceof Error) {
			return self::createErrorResponse($content, $status);
		}

		// JsonResponse
		if ($content instanceof JsonSerializable || is_array($content)) {
			return self::createJsonResponse($content, $status);
		}

		// ViewResponse
		if ($content instanceof View) {
			return self::createViewResponse($content, $status);
		}

		// StatusResponse
		if ($content instanceof StatusCode || is_int($content)) {
			return self::createStatusResponse($content, $status);
		}

		return new DefaultResponse($content, $status, $this->config);
	}

	// -----------------

	public function createFileResponse(File $content, StatusCode|int $status = StatusCode::Found): FileResponse
	{
		return new FileResponse($content, $status, $this->config);
	}

	public function createRedirectResponse(UrlObject|string|null $location = null, StatusCode|int $status = StatusCode::Found): RedirectResponse
	{
		return new RedirectResponse($location, $status, $this->config);
	}

	public function createErrorResponse(Throwable|Error|array $error, StatusCode|int $status = StatusCode::Ok): ErrorResponse
	{
		return new ErrorResponse($error, $status, $this->config);
	}

	public function createJsonResponse(JsonSerializable|array $content, StatusCode|int $status = StatusCode::Ok): JsonResponse
	{
		return new JsonResponse($content, $status, $this->config);
	}

	public function createViewResponse(View $content, StatusCode|int $status = StatusCode::Ok): ViewResponse
	{
		return new ViewResponse($content, $status, $this->config);
	}

	public function createStatusResponse(StatusCode|int $content, StatusCode|int $status = StatusCode::Ok): StatusResponse
	{
		return new StatusResponse($content, $status, $this->config);
	}

	// -----------------

	public function attachHeader(string $name, string $value): void
	{
		$name = trim($name);
		$value = trim($value);

		if (Str::length($name) > 0 && Str::length($value) > 0) {
			$this->config->set('headers.' . $name, $value);
		}
	}

	public function attachHeaders(array $headers): void
	{
		foreach ($headers as $name => $value) {
			self::attachHeader($name, $value);
		}
	}

	public function withoutHeader(string $name): void
	{
		$this->config->remove('headers.' . trim($name));
	}

	public function withoutHeaders(array $names = []): void
	{
		if (empty($names)) {
			$this->config->remove('headers');
		} else {
			foreach ($names as $name) {
				self::withoutHeader($name);
			}
		}
	}

	// -----------------

	public function attachCookie(CookieObject $cookie): void
	{
		$this->config->set('cookies.' . $cookie->name, $cookie);
	}

	public function attachCookies(array $cookies): void
	{
		foreach ($cookies as $cookie) {
			if ($cookie instanceof CookieObject) {
				$this->config->set('cookies.' . $cookie->name, $cookie);
			}
		}
	}

	public function withoutCookie(string $name): void
	{
		$this->config->remove('cookies.' . trim($name));
	}

	public function withoutCookies(): void
	{
		$this->config->remove('cookies');
	}

}