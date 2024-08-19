<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use Rovota\Framework\Facades\Registry;
use Rovota\Framework\Support\Arr;

/**
 * @internal
 */
final class CookieManager
{
	
	protected static array $plain_text = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		self::$plain_text = array_merge(Registry::array('security.plain_text_cookies'), [
			'locale', Registry::string('security.csrf.cookie_name', 'csrf_token'),
		]);
	}

	// -----------------

	public static function hasEncryptionEnabled(string $name): bool
	{
		return Arr::contains(self::$plain_text, $name) === false;
	}

}