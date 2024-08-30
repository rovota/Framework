<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching;

use Rovota\Framework\Caching\Interfaces\CacheAdapterInterface;
use Rovota\Framework\Caching\Interfaces\CacheInterface;
use Rovota\Framework\Caching\Traits\CacheFunctions;

abstract class CacheStore implements CacheInterface
{
	use CacheFunctions;

	protected string $name;

	protected CacheStoreConfig $config;

	protected CacheAdapterInterface $adapter;

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
		return CacheManager::instance()->getDefault() === $this->name;
	}

	// -----------------

	public function getName(): string
	{
		return $this->name;
	}

	public function getConfig(): CacheStoreConfig
	{
		return $this->config;
	}

	public function getAdapter(): CacheAdapterInterface
	{
		return $this->adapter;
	}

	// -----------------

	protected function getRetentionPeriod(int|null $retention): int
	{
		return $retention ?? $this->config->retention;
	}

}