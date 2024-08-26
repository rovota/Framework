<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use Rovota\Framework\Database\Interfaces\CastInterface;
use Rovota\Framework\Support\Text;

final class TextCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		return $value instanceof Text;
	}

	// -----------------

	public function toRaw(mixed $value, array $options): string
	{
		return (string) $value;
	}

	public function fromRaw(mixed $value, array $options): Text
	{
		return Text::from($value);
	}

}