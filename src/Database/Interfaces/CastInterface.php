<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Interfaces;

interface CastInterface
{

	public function supports(mixed $value, array $options): bool;

	// -----------------

	public function toRaw(mixed $value, array $options): mixed;

	public function fromRaw(mixed $value, array $options): mixed;

}