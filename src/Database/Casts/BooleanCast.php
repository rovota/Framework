<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use Rovota\Framework\Database\Interfaces\CastInterface;

final class BooleanCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		return is_bool($value);
	}

	// -----------------

	public function toRaw(mixed $value, array $options): int
	{
		return $value ? 1 : 0;
	}

	public function fromRaw(mixed $value, array $options): bool
	{
		return (int) $value === 1;
	}

}