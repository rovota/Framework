<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use Rovota\Framework\Security\Hash;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Str;

abstract class RouteEntry
{

	public RouteConfig $config {
		get {
			return $this->config;
		}
	}

	public Bucket $attributes {
		get {
			return $this->attributes;
		}
	}

	// -----------------

	public RouteEntry|null $parent = null;

	// -----------------

	public function __construct(RouteEntry|null $parent = null)
	{
		$this->config = new RouteConfig();
		$this->attributes = new Bucket();

		$this->parent = $parent;

		if ($parent !== null) {
			$this->setAttributesFromParent($parent);
		}
	}

	// -----------------

	public function getName(): string
	{
		return $this->attributes->string('name', Str::random(20));
	}

	// -----------------

	public function name(string $value): static
	{
		$value = trim($value, '.');

		if ($this->attributes->has('name')) {
			$value = implode('.', [$this->attributes->get('name'), $value]);
		}

		$this->attributes->set('name', $value);
		return $this;
	}

	public function path(string $path): static
	{
		if ($this->parent !== null) {
			$path = implode('/', [$this->parent->config->path, trim($path, '/')]);
		}

		$this->config->path = Str::start(trim($path, '/'), '/');
		return $this;
	}

	// -----------------

	public function where(array|string $parameter, string|null $pattern = null): static
	{
		$parameters = is_array($parameter) ? $parameter : [$parameter => $pattern];
		foreach ($parameters as $parameter => $pattern) {
			$this->attributes->set('parameters.' . $parameter, $pattern);
		}
		return $this;
	}

	public function whereHash(array|string $parameter, string|int $algorithm): static
	{
		$this->where($parameter, '[a-zA-Z0-9_-]{'.(is_string($algorithm) ? Hash::length($algorithm) ?? 1 : $algorithm).'}');
		return $this;
	}

	public function whereNumber(array|string $parameter, int|null $length = null): static
	{
		$this->where($parameter, '\d'.($length ? '{'.$length.'}' : '+'));
		return $this;
	}

	public function whereSlug(array|string $parameter, int|null $length = null): static
	{
		$this->where($parameter, '[a-zA-Z0-9_-]'.($length ? '{'.$length.'}' : '+'));
		return $this;
	}

	// -----------------

	public function middleware(string|array $identifiers): static
	{
		foreach (is_array($identifiers) ? $identifiers : [$identifiers] as $identifier) {
			$this->attributes->set('middleware', array_merge($this->attributes->array('middleware'), [trim($identifier)]));
		}

		return $this;
	}

	public function withoutMiddleware(string|array $identifiers): static
	{
		foreach (is_array($identifiers) ? $identifiers : [$identifiers] as $identifier) {
			$this->attributes->set('middleware_exceptions', array_merge($this->attributes->array('middleware_exceptions'), [trim($identifier)]));
		}

		return $this;
	}

	// -----------------

	protected function setAttributesFromParent(RouteEntry $parent): void
	{
		foreach ($parent->attributes as $name => $value) {
			$this->attributes->set($name, $value);
		}
	}

}