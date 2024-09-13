<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security;

use Rovota\Framework\Support\Config;

/**
 * @property-read string $key
 * @property-read string $cipher
 */
class EncryptionConfig extends Config
{

	protected static string|null $file = 'config/encryption.php';

	// -----------------

	protected function getKey(): string
	{
		return $this->get('key', getenv('CRYPT_KEY'));
	}

	/**
	 * Supports `AES-128-CBC`, `AES-256-CBC`, `AES-128-GCM` and `AES-256-GCM`.
	 */
	protected function getCipher(): string
	{
		return $this->get('cipher', getenv('CRYPT_CIPHER'));
	}

}