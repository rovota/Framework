<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security;

use Rovota\Framework\Kernel\Application;

final class Password
{

	protected function __construct()
	{
	}

	// -----------------

	public static function create(string $string): string
	{
		return password_hash($string, PASSWORD_DEFAULT, ['cost' => Application::DEFAULT_BCRYPT_COST]);
	}

	public static function verify(string $string, string $hash): bool
	{
		return password_verify($string, $hash);
	}

	public static function needsRehash(string $hash): bool
	{
		return password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => Application::DEFAULT_BCRYPT_COST]);
	}

}