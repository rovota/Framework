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
use Rovota\Framework\Facades\Encryption;
use Rovota\Framework\Identity\Enums\GuardType;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Mail\Interfaces\MailSupportsCode;
use Rovota\Framework\Mail\Mailable;
use Rovota\Framework\Security\OneTimePassword;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Support\Number;
use Throwable;

/**
 * @property int $id
 * @property int $user_id
 * @property GuardType $type
 * @property bool $encrypted
 * @property bool $preferred
 * @property mixed $content
 *
 * @property Moment|null $created
 * @property Moment|null $modified
 * @property Moment|null $trashed
 */
class Guard extends Model
{
	use Trashable;

	// -----------------

	protected array $casts = [
		'type' => ['enum', GuardType::class],
		'encrypted' => 'bool',
		'preferred' => 'bool',
	];

	protected array $guarded = [
		'id'
	];

	// -----------------

	protected function configuration(): void
	{
		$this->config->table = 'guards';
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

	public string $label {
		get => $this->type->label();
	}

	public string $description {
		get => $this->type->description();
	}

	public string|null $content {
		get {
			try {
				$content = $this->getAttribute('content');
				return $this->encrypted ? Encryption::decryptString($content) : $content;
			} catch (Throwable $throwable) {
				ExceptionHandler::logThrowable($throwable);
				return '';
			}
		}
		set {
			try {
				$this->setAttribute('content', $this->encrypted ? Encryption::encryptString($value) : $value);
			} catch (Throwable $throwable) {
				ExceptionHandler::logThrowable($throwable);
			}
		}
	}

	// -----------------

	public function prepare(array $data = []): bool
	{
		return match ($this->type) {
			GuardType::Email => $this->prepareEmail($data['mailable']),
			default => true,
		};
	}

	public function verify(mixed $input): bool
	{
		return match ($this->type) {
			GuardType::App => $this->verifyApp($input),
			GuardType::Recovery => $this->verifyRecovery($input),
			GuardType::Email => $this->verifyEmail($input),
			default => false,
		};
	}

	// -----------------

	public function setRecoveryCodes(int $amount = 6): void
	{
		$codes = [];
		while (count($codes) < $amount) {
			$codes[] = Number::random(6);
		}

		$this->content = implode(',', $codes);
	}

	// -----------------

	protected function verifyApp(string $input): bool
	{
		return OneTimePassword::from($this->content ?? '')->verify($input);
	}

	// -----------------

	protected function prepareEmail(string $mailable): bool
	{
		$timestamp = Cache::get('guard_mail_timestamp');

		if ($timestamp instanceof Moment === false || $timestamp->diffInMinutes() > 5) {

			$code = Number::random(6);

			Cache::set('guard_mail_code', $code);
			Cache::set('guard_mail_timestamp', now());

			/**
			 * @var Mailable&MailSupportsCode $mailable
			 */
			$email = new $mailable();
			$email->to($this->user->email, $this->user->nickname);
			$email->code($code);

			return $email->deliver();
		}

		return true;
	}

	protected function verifyEmail(string $input): bool
	{
		$reference = Cache::get('guard_mail_code');

		if ($reference === $input) {
			Cache::remove('guard_mail_code');
			Cache::remove('guard_mail_timestamp');
			return true;
		}

		return false;
	}

	// -----------------

	protected function verifyRecovery(string $input): bool
	{
		$codes = explode(',', $this->content ?? '');

		foreach ($codes as $key => $value) {
			if ($value === $input) {
				unset($codes[$key]);
				$this->content = implode(',', $codes);
				return $this->save();
			}
		}

		return false;
	}

}