<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use Rovota\Framework\Database\Interfaces\CastInterface;

final class FloatCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		return is_float($value);
	}

	// -----------------

	public function toRaw(mixed $value, array $options): string
	{
		return (string) $value;
	}

	public function fromRaw(mixed $value, array $options): float
	{
		return (float) $value;
	}

}