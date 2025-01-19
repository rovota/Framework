<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Model\Interfaces;

use Rovota\Framework\Database\Interfaces\ConnectionInterface;
use Rovota\Framework\Database\Model\ModelConfig;

interface ModelInterface
{

	public function __toString(): string;

	public function __set(string $name, mixed $value): void;

	public function __isset(string $name): bool;

	public function __get(string $name): mixed;

	// -----------------

	public static function newFromQueryResult(array $data): static;

	// -----------------

	public function getConfig(): ModelConfig;

	public function getTable(): string;

	public function getConnection(): ConnectionInterface;

	public function getPrimaryKey(): string;

	// -----------------

	public function toArray(): array;

	public function toJson(): string;

	public function jsonSerialize(): array;

	// -----------------

	public function getId(): string|int|null;

	// -----------------

	public function isStored(): bool;

	/**
	 * This method is only available when the specified `DELETED_COLUMN` is present.
	 */
	public function isTrashed(): bool;

	public function isRestricted(string $attribute): bool;

	public function isHidden(string $attribute): bool;

	public function isFillable(string $attribute): bool;

	public function isGuarded(string $attribute): bool;

	public function isChanged(array|string|null $attributes = null): bool;

	public function isOriginal(array|string|null $attributes = null): bool;

	// -----------------

	public function fill(array $attributes): static;

	public function attribute(string $name, mixed $default = null): mixed;

	public function original(string|null $attribute = null): mixed;

	// -----------------

	public function fresh(): static|null;

	public function reload(): void;

	public function revert(string|array|null $attribute = null): void;

	// -----------------

	public function save(): bool;

	// -----------------

	public function destroy(): bool;

	/**
	 * This method is only available when the specified `TRASHED_COLUMN` is present.
	 */
	public function trash(): bool;

	/**
	 * This method is only available when the specified `TRASHED_COLUMN` is present.
	 */
	public function recover(): bool;

	// -----------------

	public function hide(array|string $attributes): static;

	public function show(array|string $attributes): static;

	// -----------------

	public function guard(array|string $attributes): static;

	public function fillable(array|string $attributes): static;

	// -----------------

	public function hasCast(string $attribute): bool;

	public function castFromRaw(string $attribute, mixed $value): mixed;

	public function castToRaw(string $attribute, mixed $value): mixed;

}