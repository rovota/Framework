<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Interfaces;

interface CacheAdapterInterface
{

	/**
	 * Only available when using the `array` driver. Returns an empty array otherwise.
	 */
	public function all(): array;

	// -----------------

	public function has(string $key): bool;

	public function set(string $key, mixed $value, int $retention): void;

	public function get(string $key): mixed;

	public function remove(string $key): void;

	// -----------------

	public function increment(string $key, int $step = 1): void;

	public function decrement(string $key, int $step = 1): void;

	// -----------------

	public function flush(): void;

	// -----------------

	public function lastModified(): string|null;

}