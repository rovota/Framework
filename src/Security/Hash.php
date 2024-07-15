<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security;

use Rovota\Framework\Support\Str;

final class Hash
{

	protected function __construct()
	{
	}

	// -----------------

	public static function length(string $algorithm): int
	{
		if (in_array($algorithm, hash_algos())) {
			return strlen(hash($algorithm, Str::random(20)));
		}

		return 0;
	}

	// -----------------

	// TODO: Methods like fromString, fromFile, etc.

}