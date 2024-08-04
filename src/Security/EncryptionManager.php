<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security;

use Rovota\Framework\Security\Exceptions\IncorrectKeyException;
use Rovota\Framework\Support\Internal;

final class EncryptionManager
{
	protected static EncryptionAgent $agent;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
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

	public static function getAgent(): EncryptionAgent
	{
		return self::$agent;
	}

}