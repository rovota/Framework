<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching;

use Rovota\Framework\Caching\Interfaces\CacheAdapterInterface;
use Rovota\Framework\Caching\Interfaces\CacheInterface;
use Rovota\Framework\Caching\Traits\CacheFunctions;
use Rovota\Framework\Support\Str;

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

	public static function create(array $options, string|null $name = null): CacheInterface
	{
		return Caching::build($name ?? Str::random(20), $options);
	}

	// -----------------

	public function isDefault(): bool
	{
		return Caching::getDefault() === $this->name;
	}

	// -----------------

	public function name(): string
	{
		return $this->name;
	}

	public function config(): CacheStoreConfig
	{
		return $this->config;
	}

	public function adapter(): CacheAdapterInterface
	{
		return $this->adapter;
	}

	// -----------------

	protected function getRetentionPeriod(int|null $retention): int
	{
		return $retention ?? $this->config->retention;
	}

}