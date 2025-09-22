<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Models;

use Rovota\Framework\Database\Model\Model;
use Rovota\Framework\Database\Model\Traits\Trashable;
use Rovota\Framework\Facades\Cast;
use Rovota\Framework\Kernel\Resolver;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Moment;
use TypeError;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property mixed $value
 * @property string $type
 * @property Moment|null $created
 * @property Moment|null $modified
 * @property Moment|null $trashed
 */
class MetaEntity extends Model
{
	use Trashable;

	// -----------------

	protected array $restricted = [
		'type' => [
			'array',
			'bool',
			'datetime',
			'float',
			'int',
			'string',

			'text',
			'moment',
		],
	];

	protected array $guarded = ['id'];

	// -----------------

	public static function newFromQueryResult(array $data): static
	{
		$instance = parent::newFromQueryResult($data);

		if ($instance->value !== null) {
			$instance->forceValueAndCast($instance->value, $instance->type);
		}

		return $instance;
	}

	// -----------------

	protected function setValueAttribute(mixed $value): void
	{
		$type = Resolver::getValueType($value);

		if (Arr::contains($this->restricted['type'], $type) === false) {
			throw new TypeError('The type of this value is not supported.');
		}

		if (isset($this->attributes['type']) === false || $this->attributes['type'] !== $type) {
			$this->attributes_modified['type'] = $type;
		}

		$this->attributes_modified['value'] = $value;
	}

	// -----------------

	public function save(): bool
	{
		if (isset($this->attributes_modified['value'])) {

			$type = $this->attributes_modified['type'] ?? $this->attributes['type'];
			$original = $this->attributes_modified['value'];

			$this->attributes_modified['value'] = Cast::toRaw($this->attributes_modified['value'], $type);

			$result = parent::save();
			$this->attributes['value'] = $original;
			return $result;
		}

		return parent::save();
	}

	// -----------------

	public function forceValueAndCast(mixed $value, string $type): void
	{
		$this->casts['value'] = $type;
		$this->attributes['value'] = Cast::fromRaw($value, $type);
	}

}