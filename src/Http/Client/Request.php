<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use Rovota\Framework\Http\Client\Traits\RequestModifiers;
use Rovota\Framework\Http\Client\Traits\SharedModifiers;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Support\Config;

final class Request
{
	use SharedModifiers, RequestModifiers;

	// -----------------

	protected Config $config;

	protected array $parameters;

	protected Guzzle $guzzle;

	protected string $method;
	
	protected string $target;

	// -----------------

	public function __construct(Guzzle $guzzle, string $method, UrlObject|string $target)
	{
		$this->config = new Config();
		$this->parameters = [];

		$this->guzzle = $guzzle;
		$this->method = $this->getSanitizedMethod($method);
		$this->target = $this->getSanitizedTarget($target);
	}

	// -----------------

	/**
	 * @throws GuzzleException
	 */
	public function execute(): Response
	{
		return new Response($this->guzzle->request($this->method, $this->target, array_merge($this->config->toArray(), $this->parameters)));
	}

	// -----------------

	public function getRaw(): Guzzle
	{
		return $this->guzzle;
	}

	// -----------------

	protected function getSanitizedMethod(string $method): string
	{
		return match ($method) {
			'GET', 'DELETE', 'HEAD', 'OPTIONS', 'PATCH', 'POST', 'PUT' => $method,
			default => 'GET',
		};
	}

	protected function getSanitizedTarget(UrlObject|string $target): string
	{
		if (is_string($target)) {
			$target = UrlObject::from($target);
		}

		if (count($target->config->parameters) > 0) {
			$this->withParameters($target->config->parameters);
			$target->stripParameters();
		}

		$target->stripFragment();

		return $target->build();
	}

}