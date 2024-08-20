<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging;

use Rovota\Framework\Logging\Enums\Driver;
use Rovota\Framework\Support\Config;

/**
 * @property Driver|null $driver
 * @property string $label
 * @property array|null $channels
 * @property string|null $handler
 * @property Config $parameters
 */
final class ChannelConfig extends Config
{

	protected function getDriver(): Driver|null
	{
		return Driver::tryFrom($this->string('driver'));
	}

	protected function getLabel(): string
	{
		return $this->get('label', 'Unnamed Channel');
	}

	// -----------------

	protected function getChannels(): array|null
	{
		return $this->get('channels');
	}

	protected function getHandler(): string|null
	{
		return $this->get('handler');
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