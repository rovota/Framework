<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database;

use Rovota\Framework\Database\Enums\Driver;
use Rovota\Framework\Support\Config;

/**
 * @property Driver|null $driver
 * @property string $label
 * @property Config $parameters
 */
final class ConnectionConfig extends Config
{

	protected function getDriver(): Driver|null
	{
		return Driver::tryFrom($this->string('driver'));
	}

	protected function getLabel(): string
	{
		return $this->get('label', 'Unnamed Connection');
	}

	// -----------------

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