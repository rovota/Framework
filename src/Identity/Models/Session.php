<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Identity\Models;

use Rovota\Framework\Database\Model\Model;
use Rovota\Framework\Database\Model\Traits\Trashable;
use Rovota\Framework\Facades\Cache;
use Rovota\Framework\Facades\Registry;
use Rovota\Framework\Identity\Enums\SessionType;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $parent_id
 *
 * @property string $ip
 * @property string|null $client
 * @property string $hash
 *
 * @property SessionType $type
 *
 * @property bool $temporary
 * @property bool $verified
 *
 * @property Moment|null $expiration
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class Session extends Model
{
	use Trashable;

	// -----------------

	protected array $casts = [
		'type' => ['enum', SessionType::class],
		'temporary' => 'bool',
		'verified' => 'bool',
		'expiration' => 'moment',
	];

	protected array $guarded = [
		'id'
	];

	// -----------------

	protected function configuration(): void
	{
		$this->config->table = 'sessions';
	}

	// -----------------
	// Properties

	public User|null $user {
		get => Cache::store('array')->remember('user:'.$this->user_id, function() {
			return User::find($this->user_id);
		});
		set (User|null $user) {
			if ($user instanceof User) {
				$this->user_id = $user->id;
			}
			$this->user = $user;
		}
	}

	public User|null $parent {
		get => Cache::store('array')->remember('user:'.$this->parent_id, function() {
			return User::find($this->parent_id);
		});
		set (User|null $user) {
			if ($user instanceof User) {
				$this->parent_id = $user->id;
			}
			$this->user = $user;
		}
	}

	// -----------------

	public static function for(User $user, array $attributes = []): static
	{
		$duration = Registry::int('security.auth.duration');

		if ($duration === 0) {
			$attributes['temporary'] = true;
		}

		return new static(array_merge([
			'user_id' => $user->id,
			'ip' => request()->ip(),
			'client' => request()->client('Unknown'),
			'hash' => Str::random(80),
			'expiration' => now()->addDays($duration === 0 ? 1 : $duration),
			'verified' => $user->guards->isEmpty(),
		], $attributes));
	}

	// -----------------

	public function expire(): bool
	{
		$this->expiration = now();
		return $this->save();
	}

	public function verify(): bool
	{
		$this->verified = true;
		return $this->save();
	}

}