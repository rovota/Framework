<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Interfaces;

interface SupportsRawValue
{

	public function toRaw(): string|int|null;

	public static function fromRaw(mixed $value): static;

}