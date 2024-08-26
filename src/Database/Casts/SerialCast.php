<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use Rovota\Framework\Database\Interfaces\CastInterface;

final class SerialCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		return true;
	}

	// -----------------

	public function toRaw(mixed $value, array $options): string
	{
		return serialize($value);
	}

	public function fromRaw(mixed $value, array $options): mixed
	{
		return unserialize($value);
	}

}