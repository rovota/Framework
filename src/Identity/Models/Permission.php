<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Identity\Models;

use Rovota\Framework\Database\Model\Model;
use Rovota\Framework\Database\Model\Traits\Trashable;
use Rovota\Framework\Support\Moment;

/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string|null $description
 * @property string $section
 *
 * @property Moment|null $created
 * @property Moment|null $modified
 * @property Moment|null $trashed
 */
class Permission extends Model
{
	use Trashable;

	// -----------------

	protected array $guarded = [
		'id'
	];

	// -----------------

	protected function configuration(): void
	{
		$this->config->table = 'permissions';
	}

}