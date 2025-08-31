<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth;

use Rovota\Framework\Auth\Interfaces\ProviderAdapterInterface;
use Rovota\Framework\Auth\Traits\ProviderFunctions;

abstract class Provider
{
	use ProviderFunctions;

	// -----------------

	public string $name {
		get => $this->name;
	}

	public ProviderConfig $config {
		get => $this->config;
	}

	public ProviderAdapterInterface $adapter {
		get => $this->adapter;
	}

	// -----------------

	public function __construct(string $name, ProviderAdapterInterface $adapter, ProviderConfig $config)
	{
		$this->name = $name;
		$this->config = $config;
		$this->adapter = $adapter;
	}

	// -----------------

	public function __toString(): string
	{
		return $this->name;
	}

	// -----------------

	public function isDefault(): bool
	{
		return AuthManager::instance()->default === $this->name;
	}

}