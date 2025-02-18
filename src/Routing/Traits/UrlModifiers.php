<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Routing\Traits;

use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Routing\Enums\Scheme;

trait UrlModifiers
{

	public function withScheme(Scheme|string $scheme): static
	{
		$this->config->scheme = $scheme;
		return $this;
	}
	
	// -----------------

	public function withSubdomain(string|null $subdomain): static
	{
		$this->config->subdomain = $subdomain;
		return $this;
	}

	public function withDomain(string $domain): static
	{
		$this->config->domain = $domain;
		return $this;
	}

	public function withPort(int $port): static
	{
		$this->config->port = $port;
		return $this;
	}

	// -----------------

	public function withPath(string $path): static
	{
		$this->config->path = $path;
		return $this;
	}

	public function withParameter(string $name, mixed $value): static
	{
		$this->config->set('parameters.'.$name, $value);
		return $this;
	}

	public function withParameters(array $parameters): static
	{
		foreach ($parameters as $name => $value) {
			$this->config->set('parameters.'.$name, $value);
		}
		return $this;
	}

	public function withFragment(string|null $fragment): static
	{
		$this->config->fragment = $fragment;
		return $this;
	}

	// -----------------

	public function setCurrentHostAsDomain(): static
	{
		$this->withDomain(RequestManager::instance()->current()->targetHost());
		return $this;
	}

	// -----------------

	public function stripSubdomain(): static
	{
		$this->config->remove('subdomain');
		return $this;
	}

	public function stripPath(): static
	{
		$this->config->remove('path');
		return $this;
	}

	public function stripParameters(): static
	{
		$this->config->remove('parameters');
		return $this;
	}

	public function stripFragment(): static
	{
		$this->config->remove('fragment');
		return $this;
	}

}