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
use Rovota\Framework\Identity\Enums\SuspensionType;
use Rovota\Framework\Support\Moment;

/**
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property string $reason
 * @property SuspensionType $type
 *
 * @property Moment|null $expiration
 * @property Moment|null $created
 * @property Moment|null $modified
 * @property Moment|null $trashed
 */
class Suspension extends Model
{
	use Trashable;

	// -----------------

	protected array $casts = [
		'expiration' => 'moment',
		'type' => ['enum', SuspensionType::class],
	];

	protected array $guarded = [
		'id'
	];

	// -----------------

	protected function configuration(): void
	{
		$this->config->table = 'suspensions';
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

	// -----------------

	public static function for(User $user, array $attributes = []): static
	{
		return new static(array_merge([
			'user_id' => $user->id,
			'code' => 'MISC',
			'reason' => 'No reason has been provided.',
			'expiration' => now()->addDays(7),
			'type' => SuspensionType::Automatic,
		], $attributes));
	}

	// -----------------

	public function expire(): bool
	{
		$this->expiration = now();
		return $this->save();
	}

}