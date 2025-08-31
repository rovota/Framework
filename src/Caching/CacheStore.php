<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching;

use Rovota\Framework\Caching\Interfaces\CacheAdapterInterface;
use Rovota\Framework\Caching\Traits\CacheFunctions;

abstract class CacheStore
{
	use CacheFunctions;

	// -----------------

	public string $name {
		get => $this->name;
	}

	public CacheStoreConfig $config {
		get => $this->config;
	}

	public CacheAdapterInterface $adapter {
		get => $this->adapter;
	}

	// -----------------

	public function __construct(string $name, CacheAdapterInterface $adapter, CacheStoreConfig $config)
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
		return CacheManager::instance()->default === $this->name;
	}

	// -----------------

	protected function getRetentionPeriod(int|null $retention): int
	{
		return $retention ?? $this->config->retention;
	}

}