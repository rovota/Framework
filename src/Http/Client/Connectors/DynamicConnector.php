<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client\Connectors;

use Rovota\Framework\Support\Config;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\HasTimeout;

class DynamicConnector extends Connector
{
	use HasTimeout;

	public string $base_url;

	// -----------------

	protected Config $options;

	protected int $connectTimeout;

	protected int $requestTimeout;

	// -----------------

	public function __construct(string $base_url, Config $options, array $timeouts = [])
	{
		$this->base_url = $base_url;
		$this->options = $options;

		$this->connectTimeout = $timeouts['connection'];
		$this->requestTimeout = $timeouts['request'];
	}

	// -----------------

	public function overrideBaseUrl(string $base_url): void
	{
		$this->base_url = $base_url;
	}

	// -----------------

	public function resolveBaseUrl(): string
	{
		return $this->base_url;
	}

	// -----------------

	protected function defaultConfig(): array
	{
		return $this->options->toArray();
	}

}