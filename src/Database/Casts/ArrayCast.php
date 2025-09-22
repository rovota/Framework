<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use Rovota\Framework\Database\Interfaces\CastInterface;

final class ArrayCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		return is_array($value);
	}

	// -----------------

	public function toRaw(mixed $value, array $options): string
	{
		if (!is_array($value)) {
			return '';
		}

		return implode($options[0] ?? ',', $value);
	}

	public function fromRaw(mixed $value, array $options): array
	{
		$separator = $options[0] ?? ',';

		if (is_string($value)) {
			if (str_contains($value, $separator)) {
				return explode($separator, $value);
			}
			return mb_strlen($value) > 0 ? [$value] : [];
		}

		return [];
	}

}