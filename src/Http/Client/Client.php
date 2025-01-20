<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\RedirectMiddleware;
use Rovota\Framework\Http\Client\Traits\ClientModifiers;
use Rovota\Framework\Http\Client\Traits\SharedModifiers;
use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Support\Config;

class Client
{
	use SharedModifiers, ClientModifiers;

	// -----------------

	protected Config $config;

	// -----------------

	public function __construct(mixed $options = [])
	{
		$this->config = $this->getDefaultConfig()->import($options);
	}

	// -----------------

	public function request(string $method, UrlObject|string $target): Request
	{
		return $this->getRequestInstance($method, $target);
	}

	// -----------------

	public function get(UrlObject|string $target): Request
	{
		return $this->getRequestInstance('GET', $target);
	}

	public function delete(UrlObject|string $target): Request
	{
		return $this->getRequestInstance('DELETE', $target);
	}

	public function head(UrlObject|string $target): Request
	{
		return $this->getRequestInstance('HEAD', $target);
	}

	public function options(UrlObject|string $target): Request
	{
		return $this->getRequestInstance('OPTIONS', $target);
	}

	public function patch(UrlObject|string $target): Request
	{
		return $this->getRequestInstance('PATCH', $target);
	}

	public function post(UrlObject|string $target): Request
	{
		return $this->getRequestInstance('POST', $target);
	}

	public function put(UrlObject|string $target): Request
	{
		return $this->getRequestInstance('PUT', $target);
	}

	// -----------------

	private function getRequestInstance(string $method, UrlObject|string $target): Request
	{
		return new Request(new Guzzle($this->config->toArray()), $method, $target);
	}

	private function getDefaultConfig(): Config
	{
		return new Config([
			'allow_redirects' => RedirectMiddleware::$defaultSettings,
			'http_errors' => false,
			'decode_content' => true,
			'verify' => true,
			'cookies' => false,

			'version' => 2.0,
			'connect_timeout' => 2.0,
			'timeout' => 4.0,

			'headers' => [
				'User-Agent' => $this->getClientUseragent(),
			],
		]);
	}

	private function getClientUseragent(): string
	{
		$version = Framework::version()->basic();
		$name = Framework::environment()->server->get('server_name');

		return sprintf('RovotaClient/%s (+%s)', $version, $name);
	}

}