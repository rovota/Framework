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
use Rovota\Framework\Database\Interfaces\ConnectionInterface;
use Rovota\Framework\Database\Model\Interfaces\ModelInterface;
use Rovota\Framework\Database\Model\Traits\ModelQueryFunctions;
use Rovota\Framework\Database\Query\Extensions\DeleteQuery;
use Rovota\Framework\Database\Query\Extensions\UpdateQuery;
use Rovota\Framework\Database\Query\Query;
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
	const string TRASHED_COLUMN = 'deleted';

	// -----------------

	protected array $attributes = [];
	protected array $attributes_modified = [];

	protected array $casts = [];
	protected array $restricted = [];
	protected array $hidden = [];
	protected array $fillable = [];
	protected array $guarded = [];

	// -----------------

	protected ModelConfig $config;

	// -----------------

	public function __construct(array $attributes = [])
	{
		$this->setDefaultConfig();
		$this->configuration();

		if ($this->config->manage_timestamps) {
			$this->casts = array_merge($this->casts, [
				static::CREATED_COLUMN => 'moment',
				static::EDITED_COLUMN => 'moment',
				static::TRASHED_COLUMN => 'moment',
			]);
		}

		$this->setAttributes($attributes);

		$this->eventPopulated();
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

		$instance->eventPopulatedFromResult();

		return $instance;
	}

	// -----------------

	protected function configuration(): void
	{

	}

	// -----------------

	public function getConfig(): ModelConfig
	{
		return $this->config;
	}

	public function getTable(): string
	{
		return $this->config->table;
	}

	public function getConnection(): ConnectionInterface
	{
		return ConnectionManager::instance()->get($this->config->connection);
	}

	public function getPrimaryKey(): string
	{
		return $this->config->primary_key;
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

	public function getId(): string|int|null
	{
		return $this->getAttribute($this->config->primary_key);
	}

	// -----------------

	public function isStored(): bool
	{
		return $this->config->is_stored;
	}

	public function isDeleted(): bool
	{
		return $this->getAttribute(static::TRASHED_COLUMN) !== null;
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
		foreach ($attributes as $attribute) {
			if (isset($this->attributes_modified[$attribute])) {
				return true;
			}
		}

		return false;
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
		return $this->getQueryBuilder()->select()->find($this->getId(), $this->getPrimaryKey());
	}

	public function reload(): void
	{
		$new = $this->fresh();
		$this->attributes = [];
		$this->attributes_modified = [];
		foreach ($new->original() as $name => $value) {
			$this->attributes[$name] = $value;
		}
		$this->eventReloaded();
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
				$this->eventRevertedAttribute($attribute);
			} else {
				$this->attributes_modified = [];
				$this->eventReverted();
			}
		}
	}

	// -----------------

	public function save(): bool
	{
		if ($this->config->is_stored) {
			if (empty($this->attributes_modified) === false) {
				if (!isset($this->attributes_modified[self::EDITED_COLUMN]) && $this->config->manage_timestamps) {
					$this->attributes_modified[self::EDITED_COLUMN] = now();
				}
				if ($this->getUpdateQuery()->set($this->attributes_modified)->submit()) {
					$this->attributes = array_merge($this->attributes, $this->attributes_modified);
					$this->attributes_modified = [];

					$this->eventUpdated();
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

				if (empty($this->attributes[$this->getPrimaryKey()]) && !$this->config->auto_increment) {
					throw new TypeError("A primary key must be defined when auto_increment is disabled.");
				}

				if ($this->getQueryBuilder()->insert()->data($this->attributes)->submit()) {
					$this->config->is_stored = true;
					$this->attributes[$this->getPrimaryKey()] = $this->getConnection()->getHandler()->getLastId();
					$this->attributes_modified = [];

					$this->eventCreated();
				}
			}
		}

		return true;
	}

	// -----------------

	public function delete(): bool
	{
		$result = $this->getDeleteQuery()->submit();

		if ($result) {
			$this->cleanAfterDeleted();
			return true;
		}

		return false;
	}

	public function trash(): bool
	{
		$result = $this->getUpdateQuery()->trash(static::TRASHED_COLUMN)->submit();

		if ($result) {
			$this->cleanAfterTrashed();
			return true;
		}

		return false;
	}

	public function recover(): bool
	{
		$result = $this->getUpdateQuery()->recover(static::TRASHED_COLUMN)->submit();

		if ($result) {
			$this->cleanAfterRecovered();
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

	public function eventPopulated(): void
	{
	}

	public function eventPopulatedFromResult(): void
	{
	}

	public function eventReloaded(): void
	{
	}

	public function eventUpdated(): void
	{
	}

	public function eventCreated(): void
	{
	}

	public function eventTrashed(): void
	{
	}

	public function eventRecovered(): void
	{
	}

	public function eventReverted(): void
	{
	}

	public function eventRevertedAttribute(string $attribute): void
	{
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
			if ($this->config->enable_composites) {
				$accessor = sprintf('set%sAttribute', Str::pascal($name));
				if (method_exists($this, $accessor)) {
					$this->{$accessor}($value);
					return;
				}
			}

			if (isset($this->attributes[$name]) === false) {
				$this->attributes[$name] = $value;
			}

			if (is_scalar($value) && $this->attributes[$name] !== $value) {
				$this->attributes_modified[$name] = $value;
			}

			if ($value instanceof BackedEnum && $value !== $this->attributes[$name]) {
				$this->attributes_modified[$name] = $value;
			}
		}
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

		if ($this->config->enable_composites) {
			$accessor = sprintf('get%sAttribute', Str::pascal($name));
			if (method_exists($this, $accessor)) {
				return $this->{$accessor}();
			}
		}

		return $value;
	}

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
			if (is_string($this->casts[$attribute])) {
				$this->casts[$attribute] = [$this->casts[$attribute]];
			}
			if (CastingManager::instance()->isAllowedValueForCast($value, $this->casts[$attribute]) === false) {
				throw new TypeError(
					sprintf("Value must be supported by the '%s' cast, %s given", $this->casts[$attribute][0], is_object($value) ? $value::class : gettype($value))
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
					return false;
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
	protected function setDefaultConfig(): void
	{
		$this->config = new ModelConfig();
		$this->config->attachModelReference($this);
	}

	// -----------------

	/**
	 * @internal
	 */
	protected static function newInstance(array $attributes = [], bool $stored = false): static
	{
		$instance = new static($attributes);
		$instance->config->is_stored = $stored;

		return $instance;
	}

	// -----------------

	/**
	 * @internal
	 */
	protected static function getQueryBuilderFromStaticModel(): Query
	{
		$model = new static();
		return $model->getConnection()->query(['model' => $model]);
	}

	/**
	 * @internal
	 */
	protected function getQueryBuilder(): Query
	{
		return ConnectionManager::instance()->get($this->config->connection)->query([
			'model' => $this
		]);
	}

	/**
	 * @internal
	 */
	protected function getUpdateQuery(): UpdateQuery
	{
		return $this->getQueryBuilder()->update()->where($this->getPrimaryKey(), $this->getId());
	}

	/**
	 * @internal
	 */
	protected function getDeleteQuery(): DeleteQuery
	{
		return $this->getQueryBuilder()->delete()->where($this->getPrimaryKey(), $this->getId());
	}

	// -----------------

	/**
	 * @internal
	 */
	protected function cleanAfterDeleted(): void
	{
		$this->config->is_stored = false;
	}

	/**
	 * @internal
	 */
	protected function cleanAfterTrashed(): void
	{
		$this->attributes[static::TRASHED_COLUMN] = now();
	}

	/**
	 * @internal
	 */
	protected function cleanAfterRecovered(): void
	{
		$this->attributes[static::TRASHED_COLUMN] = null;
	}

}