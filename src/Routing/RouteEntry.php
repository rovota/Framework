<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

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

	public function getAttributes(): Bucket
	{
		return $this->attributes;
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