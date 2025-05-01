<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Model;

use BackedEnum;
use JsonSerializable;
use Rovota\Framework\Database\CastingManager;
use Rovota\Framework\Database\ConnectionManager;
use Rovota\Framework\Database\Events\ModelPopulated;
use Rovota\Framework\Database\Events\ModelPopulatedFromResult;
use Rovota\Framework\Database\Events\ModelReloaded;
use Rovota\Framework\Database\Events\ModelReverted;
use Rovota\Framework\Database\Events\ModelRevertedAttribute;
use Rovota\Framework\Database\Events\ModelSaved;
use Rovota\Framework\Database\Events\ModelUpdated;
use Rovota\Framework\Database\Interfaces\ConnectionInterface;
use Rovota\Framework\Database\Model\Interfaces\ModelInterface;
use Rovota\Framework\Database\Model\Traits\ModelQueryFunctions;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Traits\Conditionable;
use Rovota\Framework\Support\Traits\Macroable;
use Rovota\Framework\Support\Traits\MagicMethods;
use TypeError;

abstract class Model implements ModelInterface, JsonSerializable
{
	use MagicMethods, Macroable, Conditionable, ModelQueryFunctions;

	// -----------------

	const string CREATED_COLUMN = 'created';
	const string EDITED_COLUMN = 'edited';

	// -----------------

	protected array $attributes = [];
	protected array $attributes_modified = [];

	protected array $casts = [];
	protected array $restricted = [];
	protected array $hidden = [];
	protected array $fillable = [];
	protected array $guarded = [];

	// -----------------

	public ModelConfig $config {
		get => $this->config;
	}

	public ConnectionInterface $connection {
		get => ConnectionManager::instance()->get($this->config->connection);
	}

	// -----------------

	public function __construct(array $attributes = [])
	{
		$this->config = new ModelConfig();
		$this->config->attachModelReference($this);

		if ($this->config->manage_timestamps) {
			$this->casts = array_merge($this->casts, [
				static::CREATED_COLUMN => 'moment',
				static::EDITED_COLUMN => 'moment',
			]);
		}

		if (defined($this::class . '::TRASHED_COLUMN')) {
			$this->casts = array_merge($this->casts, [
				static::TRASHED_COLUMN => 'moment',
			]);
		}

		$this->configuration();
		$this->setAttributes($attributes);

		ModelPopulated::dispatch($this);
	}

	// -----------------

	public function __toString(): string
	{
		return $this->toJson();
	}

	public function __set(string $name, mixed $value): void
	{
		$this->setAttribute($name, $value);
	}

	public function __isset(string $name): bool
	{
		return $this->hasAttribute($name);
	}

	public function __get(string $name): mixed
	{
		return $this->getAttribute($name);
	}

	// -----------------

	/**
	 * @internal
	 */
	public static function newFromQueryResult(array $data): static
	{
		$instance = self::newInstance([], true);

		foreach ($data as $attribute => $value) {
			$instance->setRawAttribute($attribute, $value);
		}

		ModelPopulatedFromResult::dispatch($instance);

		return $instance;
	}

	// -----------------

	protected function configuration(): void
	{

	}

	// -----------------

	public function toArray(): array
	{
		return Bucket::from(array_merge($this->attributes, $this->attributes_modified))->except($this->hidden)->toArray();
	}

	public function toJson(): string
	{
		return json_encode_clean($this->toArray());
	}

	public function jsonSerialize(): array
	{
		return $this->toArray();
	}

	// -----------------

	public function isStored(): bool
	{
		return $this->config->stored;
	}

	public function isRestricted(string $attribute): bool
	{
		return isset($this->restricted[$attribute]);
	}

	public function isHidden(string $attribute): bool
	{
		return isset($this->hidden[$attribute]);
	}

	public function isFillable(string $attribute): bool
	{
		return $this->isAttributeFillable($attribute) === true;
	}

	public function isGuarded(string $attribute): bool
	{
		return $this->isAttributeFillable($attribute) === false;
	}

	public function isChanged(array|string|null $attributes = null): bool
	{
		if ($attributes === null) {
			return empty($this->attributes_modified) === false;
		}

		$attributes = is_array($attributes) ? $attributes : [$attributes];

		return array_any($attributes, fn($attribute) => isset($this->attributes_modified[$attribute]));
	}

	public function isOriginal(array|string|null $attributes = null): bool
	{
		return $this->isChanged($attributes) === false;
	}

	// -----------------

	public function fill(array $attributes): static
	{
		$this->setAttributes($attributes);
		return $this;
	}

	public function attribute(string $name, mixed $default = null): mixed
	{
		return $this->getAttribute($name) ?? $default;
	}

	public function original(string|null $attribute = null): mixed
	{
		return $attribute === null ? $this->attributes : $this->attributes[$attribute] ?? null;
	}

	// -----------------

	public function fresh(): static|null
	{
		$result = $this->getQueryBuilder()->select()->find($this->getId(), $this->config->primary_key);
		return $result instanceof static ? $result : null;
	}

	public function reload(): void
	{
		$new = $this->fresh();

		$this->attributes = [];
		$this->attributes_modified = [];

		foreach ($new->original() as $name => $value) {
			$this->attributes[$name] = $value;
		}

		ModelReloaded::dispatch($this);
	}

	public function revert(string|array|null $attribute = null): void
	{
		if (is_array($attribute)) {
			foreach ($attribute as $item) {
				$this->revert($item);
			}
		} else {
			if ($attribute !== null) {
				unset($this->attributes_modified[$attribute]);
				ModelRevertedAttribute::dispatch($this, $attribute);
			} else {
				$this->attributes_modified = [];
				ModelReverted::dispatch($this);
			}
		}
	}

	// -----------------

	public function save(): bool
	{
		if ($this->config->stored) {
			if (empty($this->attributes_modified) === false) {

				if (!isset($this->attributes_modified[self::EDITED_COLUMN]) && $this->config->manage_timestamps) {
					$this->attributes_modified[self::EDITED_COLUMN] = now();
				}

				if ($this->getUpdateQuery()->set($this->attributes_modified)->submit()) {
					$this->attributes = array_merge($this->attributes, $this->attributes_modified);
					$this->attributes_modified = [];

					ModelUpdated::dispatch($this);
				}
			}
		} else {
			$this->attributes = array_merge($this->attributes, $this->attributes_modified);

			foreach ($this->attributes as $attribute => $value) {
				if ($this->attributes[$attribute] === null) {
					unset($this->attributes[$attribute]);
				}
			}

			if (empty($this->attributes) === false) {
				if (!isset($this->attributes[self::CREATED_COLUMN]) && $this->config->manage_timestamps) {
					$this->attributes[self::CREATED_COLUMN] = now();
				}

				if (empty($this->attributes[$this->config->primary_key]) && !$this->config->auto_increment) {
					throw new TypeError("A primary key must be defined when auto_increment is disabled.");
				}

				if ($this->getQueryBuilder()->insert()->data($this->attributes)->submit()) {
					$this->config->stored = true;
					$this->attributes[$this->config->primary_key] = $this->connection->lastId();
					$this->attributes_modified = [];

					ModelSaved::dispatch($this);
				}
			}
		}

		return true;
	}

	// -----------------

	public function destroy(): bool
	{
		$result = $this->getDeleteQuery()->submit();

		if ($result) {
			$this->config->stored = false;
			return true;
		}

		return false;
	}

	// -----------------

	public function hide(array|string $attributes): static
	{
		if (is_array($attributes)) {
			foreach ($attributes as $attribute) {
				$this->hide($attribute);
			}
		} else {
			$this->hidden[] = $attributes;
		}
		return $this;
	}

	public function show(array|string $attributes): static
	{
		if (is_array($attributes)) {
			foreach ($attributes as $attribute) {
				$this->show($attribute);
			}
		} else {
			if (($key = array_search($attributes, $this->hidden)) !== false) {
				unset($this->hidden[$key]);
			}
		}
		return $this;
	}

	// -----------------

	public function guard(array|string $attributes): static
	{
		if (is_array($attributes)) {
			foreach ($attributes as $attribute) {
				$this->guard($attribute);
			}
		} else {
			if (empty($this->fillable) === false) {
				if (($key = array_search($attributes, $this->fillable)) !== false) {
					unset($this->fillable[$key]);
				}
			} else {
				if (in_array($attributes, $this->guarded) === false) {
					$this->guarded[] = $attributes;
				}
			}
		}
		return $this;
	}

	public function fillable(array|string $attributes): static
	{
		if (is_array($attributes)) {
			foreach ($attributes as $attribute) {
				$this->fillable($attribute);
			}
		} else {
			if (empty($this->guarded) === false) {
				if (($key = array_search($attributes, $this->guarded)) !== false) {
					unset($this->guarded[$key]);
				}
			} else {
				if (in_array($attributes, $this->fillable) === false) {
					$this->fillable[] = $attributes;
				}
			}
		}
		return $this;
	}

	// -----------------

	public function hasCast(string $attribute): bool
	{
		return isset($this->casts[$attribute]);
	}

	public function castFromRaw(string $attribute, mixed $value): mixed
	{
		if (!isset($this->casts[$attribute]) || $value === null) {
			return $value;
		}
		return CastingManager::instance()->castFromRaw($value, $this->casts[$attribute]);
	}

	public function castToRaw(string $attribute, mixed $value): mixed
	{
		if (!isset($this->casts[$attribute]) || $value === null) {
			return $value;
		}
		return CastingManager::instance()->castToRaw($value, $this->casts[$attribute]);
	}

	// -----------------

	/**
	 * @internal
	 */
	protected function hasAttribute(string $name): bool
	{
		return isset($this->attributes[$name]) || isset($this->attributes_modified[$name]);
	}

	/**
	 * @internal
	 */
	protected function setAttribute(string $name, mixed $value): void
	{
		if ($this->isAllowedAttributeValue($name, $value)) {
			if ($this->config->composites) {
				$accessor = sprintf('set%sAttribute', Str::pascal($name));
				if (method_exists($this, $accessor)) {
					$this->{$accessor}($value);
					return;
				}
			}

			if ($this->isStored() === false) {
				$this->attributes[$name] = $value;
			} else {

				if (isset($this->attributes[$name])) {
					$current_value = $this->getNormalizedAttributeValue($name);

					if ($value instanceof BackedEnum && ($value->value !== $current_value)) {
						$this->attributes_modified[$name] = $value;
						return;
					}

					if (is_object($value) && (spl_object_hash($value) !== $current_value)) {
						$this->attributes_modified[$name] = $value;
						return;
					}

					if (is_scalar($value) && $this->attributes[$name] !== $value) {
						$this->attributes_modified[$name] = $value;
						return;
					}

					if ($value === null && $this->attributes[$name] !== null) {
						$this->attributes_modified[$name] = $value;
					}

				} else {
					$this->attributes_modified[$name] = $value;
				}
			}
		}
	}

	protected function getNormalizedAttributeValue(string $name): mixed
	{
		if (isset($this->attributes[$name]) === false) {
			return null;
		}

		return match (true) {
			$this->attributes[$name] instanceof BackedEnum => $this->attributes[$name]->value,
			is_object($this->attributes[$name]) => spl_object_hash($this->attributes[$name]),
			default => $this->attributes[$name],
		};
	}

	/**
	 * @internal
	 */
	protected function setAttributes(array $attributes): void
	{
		foreach ($attributes as $name => $value) {
			if ($this->isAttributeFillable($name)) {
				$this->setAttribute($name, $value);
			}
		}
	}

	/**
	 * @internal
	 */
	protected function getAttribute(string $name): mixed
	{
		$value = $this->attributes_modified[$name] ?? $this->attributes[$name] ?? null;

		if ($this->config->composites) {
			$accessor = sprintf('get%sAttribute', Str::pascal($name));
			if (method_exists($this, $accessor)) {
				return $this->{$accessor}();
			}
		}

		return $value;
	}

	// -----------------

	/**
	 * @internal
	 */
	protected function isAttributeFillable(string $name): bool
	{
		if (empty($this->fillable) === false) {
			return in_array($name, $this->fillable);
		}

		if (empty($this->guarded) === false) {
			return in_array($name, $this->guarded) === false;
		}

		return false;
	}

	/**
	 * @internal
	 */
	protected function isAllowedAttributeValue(string $attribute, mixed $value): bool
	{
		if ($value === null) {
			return true;
		}

		if (isset($this->casts[$attribute])) {
			$cast = convert_to_array($this->casts[$attribute]);
			if (CastingManager::instance()->isAllowedValueForCast($value, $cast) === false) {
				throw new TypeError(
					sprintf("Value must be supported by the '%s' cast, %s given", $cast[0], is_object($value) ? $value::class : gettype($value))
				);
			}
		}

		if (isset($this->restricted[$attribute])) {
			$allowed = $this->restricted[$attribute];

			if (is_string($allowed) || $allowed instanceof BackedEnum) {
				if (Arr::contains($allowed::cases(), $value)) {
					throw new TypeError(
						sprintf('Value must be of type %s, %s given.', $allowed, is_object($value) ? $value::class : gettype($value))
					);
				}
			} else {
				if (Arr::contains($allowed, $value) === false) {
					throw new TypeError('Value must be one of the specified values.');
				}
			}
		}

		return true;
	}

	/**
	 * @internal
	 */
	public function setRawAttribute(string $name, mixed $value): void
	{
		$this->attributes[$name] = $this->castFromRaw($name, $value);
	}

	// -----------------

	/**
	 * @internal
	 */
	protected static function newInstance(array $attributes = [], bool $stored = false): static
	{
		$instance = new static($attributes);
		$instance->config->stored = $stored;

		return $instance;
	}

	// -----------------

	protected function getId(): string|int|null
	{
		return $this->getAttribute($this->config->primary_key);
	}

}