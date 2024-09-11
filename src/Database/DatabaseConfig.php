<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database;

use Rovota\Framework\Support\Config;

/**
 * @property-read string $default
 * @property-read array $connections
 */
class DatabaseConfig extends Config
{

	protected function getHeaders(): string
	{
		return $this->string('default', array_key_first($this->array('connections')) ?? '---');
	}

	protected function getConnections(): array
	{
		return $this->array('connections');
	}

}