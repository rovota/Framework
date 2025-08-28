<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client\Traits;

use Rovota\Framework\Http\Client\Requests\BasicRequest;
use Rovota\Framework\Http\Client\Requests\FormRequest;
use Rovota\Framework\Http\Client\Requests\JsonRequest;
use Rovota\Framework\Support\Str;

trait ClientFunctions
{

	public function request(string $endpoint, string $method): BasicRequest
	{
		return new BasicRequest(strtoupper($method), $this->getSanitizedEndpoint($endpoint), $this->connector);
	}

	// -----------------

	public function json(string $endpoint, string $method = 'POST'): JsonRequest
	{
		return new JsonRequest($method, $this->getSanitizedEndpoint($endpoint), $this->connector);
	}

	public function form(string $endpoint, string $method = 'POST'): FormRequest
	{
		return new FormRequest($method, $this->getSanitizedEndpoint($endpoint), $this->connector);
	}

	// -----------------

	public function get(string $endpoint): BasicRequest
	{
		return $this->request($endpoint, 'GET');
	}

	public function delete(string $endpoint): BasicRequest
	{
		return $this->request($endpoint, 'DELETE');
	}

	public function head(string $endpoint): BasicRequest
	{
		return $this->request($endpoint, 'HEAD');
	}

	public function options(string $endpoint): BasicRequest
	{
		return $this->request($endpoint, 'OPTIONS');
	}

	public function patch(string $endpoint): BasicRequest
	{
		return $this->request($endpoint, 'PATCH');
	}

	public function post(string $endpoint): BasicRequest
	{
		return $this->request($endpoint, 'POST');
	}

	public function put(string $endpoint): BasicRequest
	{
		return $this->request($endpoint, 'PUT');
	}

	// -----------------

	protected function getSanitizedEndpoint(string $endpoint): string
	{
		if (str_contains($endpoint, '://') === false) {
			return $endpoint;
		}

		$domain = parse_url($endpoint, PHP_URL_HOST);

		if (method_exists($this->connector, 'overrideBaseUrl')) {
			$this->connector->overrideBaseUrl(Str::before($endpoint, $domain) . $domain);
		}

		return Str::after($endpoint, $domain);
	}

}