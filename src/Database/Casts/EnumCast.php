<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use BackedEnum;
use Rovota\Framework\Database\Interfaces\CastInterface;
use Rovota\Framework\Support\Arr;

final class EnumCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		if (empty($options)) {
			return false;
		}

		return Arr::contains($options[0]::cases(), $value);
	}

	// -----------------

	public function toRaw(mixed $value, array $options): string|int
	{
		return $value->value;
	}

	public function fromRaw(mixed $value, array $options): BackedEnum
	{
		return $options[0]::from($value);
	}

}