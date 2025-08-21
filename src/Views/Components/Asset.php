<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views\Components;

use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Traits\Conditionable;
use Stringable;

abstract class Asset implements Stringable
{
	use Conditionable;

	// -----------------

	protected Bucket $config;

	// -----------------

	public function __construct(array $attributes = [])
	{
		$this->config = new Bucket();

		foreach ($attributes as $key => $value) {
			$this->withAttribute($key, $value);
		}
	}

	public function __toString(): string
	{
		return $this->formatAsHtml();
	}

	// -----------------

	public static function make(array $attributes = []): static
	{
		return new static($attributes);
	}

	// -----------------

	public function withAttribute(string $name, mixed $value = null): static
	{
		if (Str::length($name) > 0) {
			if (method_exists($this, $name)) {
				$this->{$name}($value);
			} else {
				$this->setAttribute($name, $value);
			}
		}

		return $this;
	}

	// -----------------

	protected function setAttribute(string $name, mixed $value): void
	{
		$this->config->set('attributes.' . $name, $value);
	}

	// -----------------

	abstract protected function formatAsHtml(): string;

}