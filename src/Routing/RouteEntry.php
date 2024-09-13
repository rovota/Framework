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

	protected Bucket $attributes;

	// -----------------

	public function __construct(RouteEntry|null $parent = null)
	{
		$this->attributes = new Bucket();

		if ($parent !== null) {
			$this->setAttributesFromParent($parent);
		}
	}

	// -----------------

	/**
	 * @internal
	 */
	public function getAttributes(): Bucket
	{
		return $this->attributes;
	}

	public function getName(): string
	{
		return $this->attributes->string('name', Str::random(20));
	}

	public function getPrefix(): string
	{
		return $this->attributes->string('prefix');
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

	// -----------------

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

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	protected function setAttributesFromParent(RouteEntry $parent): void
	{
		foreach ($parent->getAttributes() as $name => $value) {
			$this->attributes->set($name, $value);
		}
	}

}