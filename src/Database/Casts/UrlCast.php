<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use Rovota\Framework\Database\Interfaces\CastInterface;
use Rovota\Framework\Routing\UrlObject;

final class UrlCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		return $value instanceof UrlObject;
	}

	// -----------------

	public function toRaw(mixed $value, array $options): string
	{
		return (string)$value;
	}

	public function fromRaw(mixed $value, array $options): UrlObject
	{
		return UrlObject::from($value);
	}

}