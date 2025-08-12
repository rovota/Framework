<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Identity\Models;

use Rovota\Framework\Database\Model\Model;
use Rovota\Framework\Database\Model\Traits\Trashable;
use Rovota\Framework\Identity\Traits\RolePermissions;
use Rovota\Framework\Support\Moment;

/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string|null $description
 * @property string $section
 * @property array $permission_list
 *
 * @property Moment|null $created
 * @property Moment|null $modified
 * @property Moment|null $trashed
 */
class Role extends Model
{
	use RolePermissions, Trashable;

	// -----------------

	protected array $casts = [
		'permission_list' => 'array',
	];

	protected array $guarded = [
		'id'
	];

	// -----------------

	protected function configuration(): void
	{
		$this->config->table = 'roles';
	}

}