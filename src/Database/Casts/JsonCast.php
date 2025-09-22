<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use Rovota\Framework\Database\Interfaces\CastInterface;

final class JsonCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		return is_array($value);
	}

	// -----------------

	public function toRaw(mixed $value, array $options): string
	{
		return json_encode_clean($value);
	}

	public function fromRaw(mixed $value, array $options): object|array
	{
		if (json_validate($value) === false) {
			return [];
		}

		$json = json_decode($value, true);
		if ($json !== null) {
			return $json;
		}
		return [];
	}

}