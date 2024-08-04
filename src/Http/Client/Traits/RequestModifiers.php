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

	public function withJson(array $data): static
	{
		$this->config->set('json', $data);
		return $this;
	}

	public function withBody(string $data): static
	{
		$this->config->set('body', trim($data));
		return $this;
	}

	// -----------------

	public function withParameter(string $name, mixed $value): static
	{
		$this->config->set('query.'.$name, $value);
		return $this;
	}

	public function withParameters(array $parameters): static
	{
		foreach ($parameters as $name => $value) {
			$this->withParameter($name, $value);
		}
		return $this;
	}

}