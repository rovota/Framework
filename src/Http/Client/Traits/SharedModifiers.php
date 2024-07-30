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

	public function header(string $name, string $value): static
	{
		$this->config->set('headers.'.$name, trim($value));
		return $this;
	}

	public function headers(array $headers): static
	{
		foreach ($headers as $name => $value) {
			$this->header($name, $value);
		}
		return $this;
	}

	// -----------------

	public function useragent(string $useragent): static
	{
		$this->header('User-Agent', $useragent);
		return $this;
	}

	public function preferLocale(string $value): static
	{
		$this->header('Accept-Language', $value);
		return $this;
	}

	public function preferFresh(): static
	{
		$this->header('Cache-Control', 'no-cache');
		return $this;
	}

	// -----------------

	public function authorization(string $type, string $credentials): static
	{
		$this->header('Authorization', sprintf('%s %s', $type, $credentials));
		return $this;
	}

	public function bearer(string $token): static
	{
		$this->authorization('Bearer', $token);
		return $this;
	}

	// -----------------

	public function basicAuth(string $username, string $password): static
	{
		$this->config->set('auth', [$username, $password]);
		return $this;
	}

	// -----------------

	public function delay(int|float $milliseconds): static
	{
		$this->config->set('delay', $milliseconds);
		return $this;
	}

	public function connectTimeout(int|float $seconds): static
	{
		$this->config->set('timeout', (float) $seconds);
		return $this;
	}

	public function responseTimeout(int|float $seconds): static
	{
		$this->config->set('connect_timeout', (float) $seconds);
		return $this;
	}

}