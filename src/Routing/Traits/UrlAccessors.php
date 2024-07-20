<?php

/**
 * @copyright   Léandro Tijink
 * * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Routing\Traits;

use Rovota\Framework\Routing\Enums\Scheme;

trait UrlAccessors
{

	public function getScheme(): Scheme
	{
		return $this->scheme;
	}

	// -----------------

	public function getSubdomain(): string|null
	{
		return $this->subdomain;
	}

	public function getDomain(): string|null
	{
		return $this->domain;
	}

	public function getPort(): int|null
	{
		return $this->port;
	}

	// -----------------

	public function getPath(): string|null
	{
		return $this->port;
	}

	public function getParameters(): array
	{
		return $this->parameters;
	}

	public function getFragment(): string|null
	{
		return $this->fragment;
	}

}