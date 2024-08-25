<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Security\EncryptionAgent;
use Rovota\Framework\Security\EncryptionManager;
use Rovota\Framework\Support\Facade;

/**
 * @method static EncryptionAgent agent()
 *
 * @method static string generateKey(string $cipher, bool $encode = false)
 * @method static string encrypt(mixed $value, bool $serialize = true)
 * @method static string encryptString(string $value)
 * @method static mixed decrypt(string $payload, bool $deserialize = true)
 * @method static string|null decryptString(string $payload)
 */
final class Encryption extends Facade
{

	public static function service(): EncryptionManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return EncryptionManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'agent' => 'getAgent',
			default => function (EncryptionManager $instance, string $method, array $parameters = []) {
				return $instance->getAgent()->$method(...$parameters);
			},
		};
	}

}