<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client\Requests;

use Rovota\Framework\Facades\Registry;
use Rovota\Framework\Http\Client\Traits\RequestModifiers;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Support\Str;
use Saloon\Enums\Method;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Throwable;

class BasicRequest extends Request
{
	use RequestModifiers;

	// -----------------

	protected string $endpoint;

	protected Connector $connector;

	// -----------------

	public function __construct(string $method, string $endpoint, Connector $connector)
	{
		$this->method = Method::tryFrom($method) ?? Method::GET;
		$this->endpoint = $this->getSanitizedEndpoint($endpoint);
		$this->connector = $connector;
	}

	// -----------------

	public function resolveEndpoint(): string
	{
		return $this->endpoint;
	}

	public function send(): Response
	{
		if (Registry::bool('enable_generator_branding', true)) {
			$this->useragent(sprintf('RovotaClient/%s (+%s)', Framework::version()->basic(), request()->targetHost()));
		}

		try {
			return $this->connector->send($this);
		} catch (Throwable $throwable) {
			ExceptionHandler::handleThrowable($throwable);
			exit;
		}
	}

	// -----------------

	protected function getSanitizedEndpoint(string $target): string
	{
		return Str::before($target, '?');
	}

}