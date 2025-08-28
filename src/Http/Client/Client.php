<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client;

use Rovota\Framework\Http\Client\Traits\ClientFunctions;
use Saloon\Http\Connector;

abstract class Client
{
	use ClientFunctions;

	// -----------------

	public string $name {
		get => $this->name;
	}

	public ClientConfig $config {
		get => $this->config;
	}

	public Connector $connector {
		get => $this->connector;
	}

	// -----------------

	public function __construct(string $name, Connector $connector, ClientConfig $config)
	{
		$this->name = $name;
		$this->config = $config;
		$this->connector = $connector;
	}

	// -----------------

	public function __toString(): string
	{
		return $this->name;
	}

	// -----------------

	public function isDefault(): bool
	{
		return ClientManager::instance()->getDefault() === $this->name;
	}

}