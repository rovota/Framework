<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security;

use Rovota\Framework\Support\Config;

class EncryptionConfig extends Config
{

	protected static string|null $file = 'config/encryption.php';

	// -----------------

	public string $key {
		get => $this->get('key', getenv('CRYPT_KEY'));
	}

	/**
	 * Supports `AES-128-CBC`, `AES-256-CBC`, `AES-128-GCM` and `AES-256-GCM`.
	 */
	public string $cipher {
		get => $this->get('cipher', getenv('CRYPT_CIPHER'));
	}

}