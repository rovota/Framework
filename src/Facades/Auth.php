<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Auth\AuthManager;
use Rovota\Framework\Auth\Provider;
use Rovota\Framework\Identity\Models\Session;
use Rovota\Framework\Identity\Models\User;
use Rovota\Framework\Support\Facade;

/**
 * @method static Provider provider(string|null $name = null)
 * @method static Provider create(array $config, string|null $name = null)
 *
 * @method static Session|null session()
 * @method static User|null user()
 * @method static string|int|null id()
 *
 * @method static bool check()
 * @method static bool guest()
 *
 * @method static bool login(User $user, array $attributes = [])
 * @method static bool logout()
 *
 * @method static User|false validate(array $credentials)
 * @method static void set(User $user, Session|null $session = null)
 *
 */
final class Auth extends Facade
{

	public static function service(): AuthManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return AuthManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'provider' => 'get',
			'create' => 'createProvider',
			default => function (AuthManager $instance, string $method, array $parameters = []) {
				if ($instance->get() === null) {
					return null;
				}
				return $instance->get()->$method(...$parameters);
			},
		};
	}

}