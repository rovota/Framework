<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Http\Client\Traits;

trait ClientModifiers
{

	public function latestVersion(): static
	{
		$this->config->set('version', 3.0);
		return $this;
	}

	public function oldestVersion(): static
	{
		$this->config->set('version', 1.1);
		return $this;
	}

	public function version(int|float $version): static
	{
		$this->config->set('version', (float) $version);
		return $this;
	}

}