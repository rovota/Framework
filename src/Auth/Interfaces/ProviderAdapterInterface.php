<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth\Interfaces;

use Rovota\Framework\Identity\Models\Session;
use Rovota\Framework\Identity\Models\User;

interface ProviderAdapterInterface
{

	public Session|null $session {
		get;
		set;
	}

	public User|null $user {
		get;
		set;
	}

	// -----------------

	public function initialize(): void;

	// -----------------

	public function startSession(array $attributes = []): bool;

	public function endSession(): bool;

	public function verifySession(): bool;

	// -----------------

	public function validate(array $credentials): User|false;

}