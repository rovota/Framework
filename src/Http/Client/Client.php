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
use Rovota\Framework\Structures\Config;

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
			'idn_conversion' => false,

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
		$name = Framework::environment()->server()->get('server_name');

		return sprintf('RovotaClient/%s (+%s)', $version, $name);
	}

}