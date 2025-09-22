<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Identity\Traits;

use Rovota\Framework\Caching\Enums\Driver;
use Rovota\Framework\Facades\Cache;
use Rovota\Framework\Identity\Models\Permission;
use Rovota\Framework\Structures\Extensions\PermissionBag;

trait UserPermissions
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
			if (in_array($identifier, $this->permissions_denied ?? []) === false) {
				$permission = Cache::storeWithDriver(Driver::Memory)->remember('permission:' . $identifier, function () use ($identifier) {
					return Permission::find($identifier);
				});

				$this->permission_entities->set($permission->name, $permission);
			}
		}

		foreach ($this->role->permissions as $permission) {
			if (in_array($permission->id, $this->permissions_denied ?? []) === false) {
				$this->permission_entities->set($permission->name, $permission);
			}
		}
	}

}