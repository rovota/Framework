<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching;

use Rovota\Framework\Caching\Enums\Driver;
use Rovota\Framework\Structures\Config;

/**
 * @property Driver|null $driver
 * @property string $label
 * @property int $retention
 * @property Config $parameters
 */
final class CacheStoreConfig extends Config
{

	protected function getDriver(): Driver|null
	{
		return Driver::tryFrom($this->string('driver'));
	}

	protected function getLabel(): string
	{
		return $this->get('label', 'Unnamed Cache');
	}

	// -----------------

	protected function getRetention(): int
	{
		return $this->get('retention', 0);
	}

	protected function getParameters(): Config
	{
		return new Config($this->array('parameters'));
	}

	// -----------------

	public function isValid(): bool
	{
		return true;
	}

}