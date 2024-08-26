<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use Rovota\Framework\Database\Interfaces\CastInterface;

final class IntegerCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		return is_int($value);
	}

	// -----------------

	public function toRaw(mixed $value, array $options): int
	{
		return (int) $value;
	}

	public function fromRaw(mixed $value, array $options): int
	{
		return (int) $value;
	}

}