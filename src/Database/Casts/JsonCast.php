<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use Rovota\Framework\Database\Interfaces\CastInterface;
use Rovota\Framework\Database\Interfaces\SupportsRawValue;

final class JsonCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		return is_array($value) || $value instanceof SupportsRawValue;
	}

	// -----------------

	public function toRaw(mixed $value, array $options): string
	{
		if ($value instanceof SupportsRawValue) {
			$value = $value->toRaw();
		}
		return json_encode_clean($value);
	}

	public function fromRaw(mixed $value, array $options): object|array
	{
		if (json_validate($value) === false) {
			return [];
		}

		$json = json_decode($value, true);
		if ($json !== null) {
			if (isset($options[0]) && $options[0] !== 'array') {
				return $options[0]::fromRaw($json);
			}
			return $json;
		}
		return [];
	}

}