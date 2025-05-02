<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth\Interfaces;

use Rovota\Framework\Auth\ProviderConfig;
use Rovota\Framework\Identity\Models\Session;
use Rovota\Framework\Identity\Models\User;

interface ProviderInterface
{

	public string $name {
		get;
	}

	public ProviderConfig $config {
		get;
	}

	public ProviderAdapterInterface $adapter {
		get;
	}

	// -----------------

	public function __toString(): string;

	// -----------------

	public function isDefault(): bool;

	// -----------------

	public function session(): Session|null;

	public function user(): User|null;

	public function id(): string|int|null;

	// -----------------

	public function check(): bool;

	public function guest(): bool;

	// -----------------

	/**
	 * Specify a user that should be authenticated manually.
	 */
	public function login(User $user, array $attributes = []): bool;

	/**
	 * Sign out a user manually.
	 */
	public function logout(): bool;

	// -----------------

	/**
	 * Validate the given credentials without creating an authenticated state.
	 */
	public function validate(array $credentials): User|false;

	/**
	 * Force a specific user to be used.
	 */
	public function set(User $user, Session|null $session = null): void;

}