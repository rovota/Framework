<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Response;

use JsonSerializable;
use Rovota\Framework\Http\ApiError;
use Rovota\Framework\Http\Cookie\CookieObject;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Response\Variants\ErrorResponseObject;
use Rovota\Framework\Http\Response\Variants\JsonResponseObject;
use Rovota\Framework\Http\Response\Variants\RedirectResponseObject;
use Rovota\Framework\Http\Response\Variants\StatusResponseObject;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Support\Internal;
use Rovota\Framework\Support\Str;
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
		$this->config = ResponseConfig::load('config/responses.php');

		$this->config->set([
			'headers.X-Powered-By' => 'Rovota Framework',
			'headers.X-XSS-Protection' => '0',
		]);
	}

	// -----------------

	public function initialize(): void
	{
		$config = require Internal::projectFile('config/responses.php');

		$this->config = new Config($config);

		$this->config->set([
			'headers.X-Powered-By' => 'Rovota Framework',
			'headers.X-XSS-Protection' => '0',
		]);
	}

	// -----------------

	public function getConfig(): Config
	{
		return $this->config;
	}

	// -----------------

	public function createResponse(mixed $content, StatusCode|int $status = StatusCode::Ok): ResponseObject
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

		// StatusResponse
		if ($content instanceof StatusCode || is_int($content)) {
			return self::createStatusResponse($content, $status);
		}

		return new ResponseObject($content, $status, $this->config);
	}

	// -----------------

	public function createRedirectResponse(UrlObject|string|null $location = null, StatusCode|int $status = StatusCode::Found): RedirectResponseObject
	{
		return new RedirectResponseObject($location, $status, $this->config);
	}

	public function createErrorResponse(Throwable|ApiError|array $error, StatusCode|int $status = StatusCode::Ok): ErrorResponseObject
	{
		return new ErrorResponseObject($error, $status, $this->config);
	}

	public function createJsonResponse(JsonSerializable|array $content, StatusCode|int $status = StatusCode::Ok): JsonResponseObject
	{
		return new JsonResponseObject($content, $status, $this->config);
	}

	public function createStatusResponse(StatusCode|int $content, StatusCode|int $status = StatusCode::Ok): StatusResponseObject
	{
		return new StatusResponseObject($content, $status, $this->config);
	}

	// -----------------

	public function attachHeader(string $name, string $value): void
	{
		$name = trim($name);
		$value = trim($value);

		if (Str::length($name) > 0 && Str::length($value) > 0) {
			self::getConfig()->set('headers.'.$name, $value);
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
		self::getConfig()->remove('headers.'.trim($name));
	}

	public function withoutHeaders(array $names = []): void
	{
		if (empty($names)) {
			self::getConfig()->remove('headers');
		} else {
			foreach ($names as $name) {
				self::withoutHeader($name);
			}
		}
	}

	// -----------------

	public function attachCookie(CookieObject $cookie): void
	{
		self::getConfig()->set('cookies.'.$cookie->name, $cookie);
	}

	public function attachCookies(array $cookies): void
	{
		foreach ($cookies as $cookie) {
			if ($cookie instanceof CookieObject) {
				self::getConfig()->set('cookies.'.$cookie->name, $cookie);
			}
		}
	}

	public function withoutCookie(string $name): void
	{
		self::getConfig()->remove('cookies.'.trim($name));
	}

	public function withoutCookies(): void
	{
		self::getConfig()->remove('cookies');
	}

}