<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Identity\Models;

use Rovota\Framework\Caching\Enums\Driver;
use Rovota\Framework\Database\Model\Traits\Trashable;
use Rovota\Framework\Facades\Cache;
use Rovota\Framework\Support\Models\MetaEntity;

/**
 * @property int $user_id
 */
class UserMeta extends MetaEntity
{
	use Trashable;

	// -----------------

	protected function configuration(): void
	{
		$this->config->table = 'user_meta';
	}

	// -----------------
	// Properties

	public User|null $user {
		get => Cache::storeWithDriver(Driver::Memory)->remember('user:' . $this->user_id, function () {
			return User::find($this->user_id);
		});
		set (User|null $user) {
			if ($user instanceof User) {
				$this->user_id = $user->id;
			}
			$this->user = $user;
		}
	}

}