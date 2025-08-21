<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use Rovota\Framework\Database\Interfaces\CastInterface;
use Stringable;

final class ObjectCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		if (empty($options)) {
			return false;
		}

		return $value instanceof $options[0] && $value instanceof Stringable;
	}

	// -----------------

	public function toRaw(mixed $value, array $options): string
	{
		return (string)$value;
	}

	public function fromRaw(mixed $value, array $options): object
	{
		return new $options[0]($value);
	}

}