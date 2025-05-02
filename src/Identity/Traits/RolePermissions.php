<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Identity\Traits;

use Rovota\Framework\Facades\Cache;
use Rovota\Framework\Identity\Models\Permission;
use Rovota\Framework\Structures\Extensions\PermissionBag;

trait RolePermissions
{

	protected PermissionBag|null $permission_entities = null;

	/**
	 * @var PermissionBag<string, Permission>
	 */
	public PermissionBag $permissions {
		get {
			if ($this->permission_entities === null) {
				$this->loadPermissions();
			}
			return $this->permission_entities;
		}
		set {
			$this->permission_entities = $value;
		}
	}

	// -----------------

	protected function loadPermissions(): void
	{
		$this->permission_entities = new PermissionBag();

		foreach ($this->permission_list ?? [] as $identifier) {
			$permission = Cache::store('array')->remember('permission:'.$identifier, function() use ($identifier) {
				return Permission::find($identifier);
			});

			$this->permission_entities->set($permission->name, $permission);
		}
	}

}