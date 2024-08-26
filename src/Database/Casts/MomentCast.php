<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use Rovota\Framework\Database\Interfaces\CastInterface;
use Rovota\Framework\Support\Moment;

final class MomentCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		return $value instanceof Moment;
	}

	// -----------------

	public function toRaw(mixed $value, array $options): string
	{
		return $value->toUtc()->format($options[0] ?? 'Y-m-d H:i:s');
	}

	public function fromRaw(mixed $value, array $options): Moment
	{
		return Moment::createFromFormat($options[0] ?? 'Y-m-d H:i:s', $value);
	}

}