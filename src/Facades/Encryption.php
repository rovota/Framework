<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Rovota\Framework\Security\EncryptionAgent;
use Rovota\Framework\Security\EncryptionManager;
use Rovota\Framework\Security\Exceptions\EncryptionException;
use Rovota\Framework\Security\Exceptions\PayloadException;
use Rovota\Framework\Security\Exceptions\UnsupportedCipherException;

final class Encryption
{

	protected function __construct()
	{
	}

	// -----------------

	public static function agent(): EncryptionAgent
	{
		return EncryptionManager::getAgent();
	}

	// -----------------

	/**
	 * @throws UnsupportedCipherException
	 */
	public static function generateKey(string $cipher, bool $encode = false): string
	{
		return EncryptionManager::getAgent()->generateKey($cipher, $encode);
	}

	// -----------------

	/**
	 * @throws EncryptionException
	 */
	public static function encrypt(mixed $value, bool $serialize = true): string
	{
		return EncryptionManager::getAgent()->encrypt($value, $serialize);
	}

	/**
	 * @throws EncryptionException
	 */
	public static function encryptString(string $value): string
	{
		return EncryptionManager::getAgent()->encrypt($value, false);
	}

	/**
	 * @throws PayloadException
	 */
	public static function decrypt(string $payload, bool $deserialize = true): mixed
	{
		return EncryptionManager::getAgent()->decrypt($payload, $deserialize);
	}

	/**
	 * @throws PayloadException
	 */
	public static function decryptString(string $payload): string|null
	{
		return EncryptionManager::getAgent()->decrypt($payload, false);
	}

}