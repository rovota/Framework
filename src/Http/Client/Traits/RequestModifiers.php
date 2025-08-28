<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Http\Client\Traits;

use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Auth\HeaderAuthenticator;
use Saloon\Http\Auth\QueryAuthenticator;
use Saloon\Http\Auth\TokenAuthenticator;

trait RequestModifiers
{

	public function useragent(string $useragent): static
	{
		$this->headers()->add('User-Agent', $useragent);
		return $this;
	}

	public function preferLocale(string $value): static
	{
		$this->headers()->add('Accept-Language', $value);
		return $this;
	}

	public function preferFresh(): static
	{
		$this->headers()->add('Cache-Control', 'no-cache');
		return $this;
	}

	// -----------------

	public function withHeader(string $name, string $value): static
	{
		$this->headers()->add($name, $value);
		return $this;
	}

	public function withHeaders(array $headers): static
	{
		$this->headers()->merge($headers);
		return $this;
	}

	// -----------------

	public function useTokenAuth(string $token): static
	{
		$this->authenticate(new TokenAuthenticator($token));
		return $this;
	}

	public function useBasicAuth(string $username, string $password): static
	{
		$this->authenticate(new BasicAuthenticator($username, $password));
		return $this;
	}

	public function useHeaderAuth(string $token, string $name = 'X-TOKEN'): static
	{
		$this->authenticate(new HeaderAuthenticator($token, $name));
		return $this;
	}

	public function useQueryAuth(string $token, string $name = 'X-TOKEN'): static
	{
		$this->authenticate(new QueryAuthenticator($name, $token));
		return $this;
	}

}