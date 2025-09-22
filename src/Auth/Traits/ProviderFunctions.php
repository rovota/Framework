<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth\Traits;

use Rovota\Framework\Identity\Models\Session;
use Rovota\Framework\Identity\Models\User;

trait ProviderFunctions
{

	public function session(): Session|null
	{
		return $this->adapter->session;
	}

	public function user(): User|null
	{
		return $this->adapter->user;
	}

	public function id(): string|int|null
	{
		return $this->adapter->user?->id;
	}

	// -----------------

	public function check(): bool
	{
		return $this->adapter->user instanceof User;
	}

	public function guest(): bool
	{
		return $this->adapter->user instanceof User === false;
	}

	// -----------------

	public function login(User $user, array $attributes = []): bool
	{
		$this->adapter->user = $user;
		return $this->adapter->startSession($attributes);
	}

	public function logout(): bool
	{
		return $this->adapter->endSession();
	}

	// -----------------

	public function validate(array $credentials): User|false
	{
		return $this->adapter->validate($credentials);
	}

	public function set(User $user, Session|null $session = null): void
	{
		$this->adapter->user = $user;

		if ($session instanceof Session) {
			$this->adapter->session = $session;
		}
	}

}