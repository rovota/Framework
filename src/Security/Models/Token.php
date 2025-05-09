<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security\Models;

use Rovota\Framework\Database\Model\Model;
use Rovota\Framework\Database\Model\Traits\Trashable;
use Rovota\Framework\Facades\Cache;
use Rovota\Framework\Identity\Models\User;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $ip
 * @property string|null $client
 * @property string $hash
 * @property string|null $code
 * @property int $uses
 *
 * @property Moment|null $expiration
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class Token extends Model
{
	use Trashable;

	// -----------------

	protected array $casts = [
		'expiration' => 'moment',
	];

	protected array $guarded = [
		'id'
	];

	// -----------------

	protected function configuration(): void
	{
		$this->config->table = 'tokens';
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

	// -----------------

	public static function for(User $user, array $attributes = []): static
	{
		return new static(array_merge([
			'user_id' => $user->id,
			'ip' => request()->ip(),
			'client' => request()->client(),
			'hash' => Str::random(100),
			'expiration' => now()->addMinutes(30),
		], $attributes));
	}

	// -----------------

	public function expire(): bool
	{
		$this->expiration = now();
		return $this->save();
	}

}