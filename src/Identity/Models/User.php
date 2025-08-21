<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Identity\Models;

use Rovota\Framework\Caching\Enums\Driver;
use Rovota\Framework\Database\Model\Model;
use Rovota\Framework\Database\Model\Traits\Trashable;
use Rovota\Framework\Facades\Cache;
use Rovota\Framework\Facades\Language;
use Rovota\Framework\Facades\Storage;
use Rovota\Framework\Identity\Traits\UserActivity;
use Rovota\Framework\Identity\Traits\UserGuards;
use Rovota\Framework\Identity\Traits\UserPermissions;
use Rovota\Framework\Localization\LanguageObject;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Support\Traits\Metadata;
use Rovota\Framework\Support\Url;

/**
 * @property int $id
 * @property string $username
 * @property string $nickname
 * @property string $email
 * @property bool $email_verified
 * @property string $password
 * @property string $locale_id
 * @property int $role_id
 * @property array $permission_list
 * @property array $permissions_denied
 * @property bool $enabled
 *
 * @property Moment|null $created
 * @property Moment|null $modified
 * @property Moment|null $trashed
 */
class User extends Model
{
	use UserActivity, UserPermissions, UserGuards, Metadata, Trashable;

	// -----------------

	protected array $casts = [
		'email_verified' => 'bool',
		'permission_list' => 'array',
		'permissions_denied' => 'array',
		'last_active' => 'moment',
		'enabled' => 'bool',
	];

	protected array $guarded = [
		'id'
	];

	// -----------------

	protected function configuration(): void
	{
		$this->config->table = 'users';
	}

	// -----------------
	// Properties

	public LanguageObject|null $language {
		get => Language::get($this->locale_id);
		set (LanguageObject|null $language) {
			if ($language instanceof LanguageObject) {
				$this->locale_id = $language->locale;
			}
			$this->language = $language;
		}
	}

	public Role|null $role {
		get => Cache::storeWithDriver(Driver::Memory)->remember('role:' . $this->role_id, function () {
			return Role::find($this->role_id);
		});
		set (Role|null $role) {
			if ($role instanceof Role) {
				$this->role_id = $role->id;
			}
			$this->role = $role;
		}
	}

	public Suspension|null $suspension {
		get => Cache::storeWithDriver(Driver::Memory)->remember('suspension:' . $this->id, function () {
			$suspension = Suspension::where(['user_id' => $this->id])->first();
			if ($suspension instanceof Suspension && ($suspension->expiration === null || $suspension->expiration->isFuture())) {
				return $suspension;
			}
			return null;
		});
	}

	public UrlObject $avatar_url {
		get {
			if ($this->metadata->has('avatar')) {
				return Url::file('avatars/' . md5($this->id) . '/' . $this->meta('avatar'));
			}
			return Url::file('avatars/default.svg');
		}
	}

}