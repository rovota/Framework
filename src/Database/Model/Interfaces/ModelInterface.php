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

	public ModelConfig $config {
		get;
	}

	public ConnectionInterface $connection {
		get;
	}

	const string CREATED_COLUMN = 'created';
	const string MODIFIED_COLUMN = 'modified';
	const string TRASHED_COLUMN = 'trashed';

	// -----------------

	public function __toString(): string;

	public function __set(string $name, mixed $value): void;

	public function __isset(string $name): bool;

	public function __get(string $name): mixed;

	// -----------------

	public static function newFromQueryResult(array $data): static;

	// -----------------

	public function toArray(): array;

	public function toJson(): string;

	public function jsonSerialize(): array;

	// -----------------

	public function isStored(): bool;

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