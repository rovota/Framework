<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security;

use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Security\Exceptions\IncorrectKeyException;

/**
 * @internal
 */
final class EncryptionManager extends ServiceProvider
{
	public EncryptionAgent $agent {
		get {
			return $this->agent;
		}
	}

	// -----------------

	/**
	 * @throws IncorrectKeyException
	 */
	public function __construct()
	{
		$config = EncryptionConfig::load('config/encryption');

		$this->agent = new EncryptionAgent(
			base64_decode($config->key), $config->cipher
		);
	}

}