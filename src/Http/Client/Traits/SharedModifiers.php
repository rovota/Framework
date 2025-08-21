<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Http\Client\Traits;

trait SharedModifiers
{

	public function set(array|string $options, mixed $value = null): static
	{
		if (is_string($options)) {
			$options = [$options => $value];
		}
		foreach ($options as $name => $value) {
			$this->config->set($name, $value);
		}
		return $this;
	}

	// -----------------

	public function withHeader(string $name, string $value): static
	{
		$this->config->set('headers.' . $name, trim($value));
		return $this;
	}

	public function withHeaders(array $headers): static
	{
		foreach ($headers as $name => $value) {
			$this->withHeader($name, $value);
		}
		return $this;
	}

	// -----------------

	public function setUseragent(string $useragent): static
	{
		$this->withHeader('User-Agent', $useragent);
		return $this;
	}

	public function preferLocale(string $value): static
	{
		$this->withHeader('Accept-Language', $value);
		return $this;
	}

	public function preferFresh(): static
	{
		$this->withHeader('Cache-Control', 'no-cache');
		return $this;
	}

	// -----------------

	public function provideAuthorization(string $type, string $credentials): static
	{
		$this->withHeader('Authorization', sprintf('%s %s', $type, $credentials));
		return $this;
	}

	public function provideBearerToken(string $token): static
	{
		$this->provideAuthorization('Bearer', $token);
		return $this;
	}

	public function provideBasicAuth(string $username, string $password): static
	{
		$this->config->set('auth', [$username, $password]);
		return $this;
	}

	// -----------------

	public function setDelay(int|float $milliseconds): static
	{
		$this->config->set('delay', $milliseconds);
		return $this;
	}

	public function setConnectTimeout(int|float $seconds): static
	{
		$this->config->set('timeout', (float)$seconds);
		return $this;
	}

	public function setResponseTimeout(int|float $seconds): static
	{
		$this->config->set('connect_timeout', (float)$seconds);
		return $this;
	}

}