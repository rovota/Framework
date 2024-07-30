<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Http\Client\Traits;

trait RequestModifiers
{

	public function json(array $data): static
	{
		$this->config->set('json', $data);
		return $this;
	}

	public function body(string $data): static
	{
		$this->config->set('body', trim($data));
		return $this;
	}

	// -----------------

	public function parameter(string $name, mixed $value): static
	{
		$this->config->set('query.'.$name, $value);
		return $this;
	}

	public function parameters(array $parameters): static
	{
		foreach ($parameters as $name => $value) {
			$this->parameter($name, $value);
		}
		return $this;
	}

}