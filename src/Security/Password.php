<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security;

use Rovota\Framework\Facades\Registry;

final class Password
{

	protected function __construct()
	{
	}

	// -----------------

	public static function create(string $string): string
	{
		return password_hash($string, PASSWORD_DEFAULT, ['cost' => Registry::int('security.password.hash_cost', 12)]);
	}

	public static function verify(string $string, string $hash): bool
	{
		return password_verify($string, $hash);
	}

	public static function needsRehash(string $hash): bool
	{
		return password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => Registry::int('security.password.hash_cost', 12)]);
	}

}