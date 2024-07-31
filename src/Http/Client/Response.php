<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client;

use Psr\Http\Message\ResponseInterface;
use Rovota\Framework\Http\Client\Traits\ResponseContent;
use Rovota\Framework\Http\Client\Traits\ResponseValidation;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Support\Arr;

final class Response
{
	use ResponseValidation, ResponseContent;

	// -----------------

	protected ResponseInterface $response;

	// -----------------

	public function __construct(ResponseInterface $response)
	{
		$this->response = $response;
	}

	// -----------------

	public function raw(): ResponseInterface
	{
		return $this->response;
	}

	// -----------------

	public function version(): int|float
	{
		$raw = (float) $this->response->getProtocolVersion();
		return floor($raw) == $raw ? (int)$raw : $raw;
	}

	// -----------------

	public function status(): StatusCode
	{
		return StatusCode::tryFrom($this->response->getStatusCode()) ?? StatusCode::Ok;
	}

	public function reason(): string|null
	{
		$raw = $this->response->getReasonPhrase();
		return strlen($raw) > 0 ? $raw : null;
	}

	// -----------------

	public function headers(): array
	{
		return $this->response->getHeaders();
	}

	public function header(string $name, string|null $default = null): string|null
	{
		return Arr::first($this->response->getHeader($name), default: $default);
	}

	public function hasHeader(string $name): bool
	{
		return $this->response->hasHeader($name);
	}

}