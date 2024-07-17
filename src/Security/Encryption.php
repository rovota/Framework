<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security;

use Rovota\Framework\Security\Exceptions\EncryptionException;
use Rovota\Framework\Security\Exceptions\IncorrectKeyException;
use Rovota\Framework\Security\Exceptions\PayloadException;
use Rovota\Framework\Security\Exceptions\UnsupportedCipherException;
use Rovota\Framework\Support\Internal;

final class Encryption
{
	protected static EncryptionAgent $agent;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @throws IncorrectKeyException
	 */
	public static function initialize(): void
	{
		$config = require Internal::projectFile('config/encryption.php');

		self::$agent = new EncryptionAgent(
			base64_decode($config['key']), $config['cipher']
		);
	}

	// -----------------

	public static function agent(): EncryptionAgent
	{
		return self::$agent;
	}

	// -----------------

	/**
	 * @throws UnsupportedCipherException
	 */
	public static function generateKey(string $cipher, bool $encode = false): string
	{
		return self::$agent->generateKey($cipher, $encode);
	}

	// -----------------

	/**
	 * @throws EncryptionException
	 */
	public static function encrypt(mixed $value, bool $serialize = true): string
	{
		return self::$agent->encrypt($value, $serialize);
	}

	/**
	 * @throws EncryptionException
	 */
	public static function encryptString(string $value): string
	{
		return self::$agent->encrypt($value, false);
	}

	/**
	 * @throws PayloadException
	 */
	public static function decrypt(string $payload, bool $deserialize = true): mixed
	{
		return self::$agent->decrypt($payload, $deserialize);
	}

	/**
	 * @throws PayloadException
	 */
	public static function decryptString(string $payload): string|null
	{
		return self::$agent->decrypt($payload, false);
	}

}