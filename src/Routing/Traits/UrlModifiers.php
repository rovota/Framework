<?php

/**
 * @copyright   Léandro Tijink
 * * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Routing\Traits;

use Rovota\Framework\Http\Request;
use Rovota\Framework\Routing\Enums\Scheme;
use Rovota\Framework\Support\Str;

trait UrlModifiers
{

	public function setScheme(Scheme|string $scheme): static
	{
		if (is_string($scheme)) {
			$scheme = Scheme::tryFrom($scheme) ?? Scheme::Https;
		}

		$this->scheme = $scheme;

		return $this;
	}
	
	// -----------------

	public function setSubdomain(string $subdomain): static
	{
		if ($this->domain === null) {
			$this->setDomain(Request::current()->targetHost());
		}

		$subdomain = trim($subdomain);

		// Set to null when unusable value is given.
		if (mb_strlen($subdomain) === 0) {
			$this->subdomain = null;
			return $this;
		}

		// Set to null when useless value is given.
		if ($subdomain === 'www' || $subdomain === '.' || $subdomain === '-') {
			$this->subdomain = null;
			return $this;
		}

		$this->subdomain = trim($subdomain);

		return $this;
	}

	public function setDomain(string $domain): static
	{
		$domain = trim($domain);

		if (mb_strlen($domain) === 0 || $domain === '-') {
			$this->setDomain(Request::current()->targetHost());
		}

		if (Str::occurrences($domain, '.') > 1) {
			$this->subdomain = Str::before($domain, '.');
			$domain = Str::after($domain, '.');
		}

		$this->domain = trim($domain);

		return $this;
	}

	public function setPort(int $port): static
	{
		$this->port = $port;
		return $this;
	}

	// -----------------

	public function setPath(string $path): static
	{
		$path = trim($path, ' /');

		// Set to null when unusable value is given.
		if (mb_strlen($path) === 0) {
			$this->path = null;
			return $this;
		}

		$this->path = $path;

		return $this;
	}

	public function setParameters(array $parameters): static
	{
		if (empty($parameters)) {
			$this->parameters = [];
			return $this;
		}

		foreach ($parameters as $name => $value) {
			$this->setParameter($name, $value);
		}

		return $this;
	}

	public function setParameter(string $name, mixed $value): static
	{
		$name = strtolower(trim($name));

		if ($value === null) {
			unset($this->parameters[$name]);
			return $this;
		}

		$this->parameters[$name] = $value;

		return $this;
	}

	public function setFragment(string $fragment): static
	{
		$fragment = trim($fragment);

		// Set to null when unusable value is given.
		if (mb_strlen($fragment) === 0) {
			$this->fragment = null;
			return $this;
		}

		$this->fragment = trim($fragment);

		return $this;
	}

}