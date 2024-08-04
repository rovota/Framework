<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use Rovota\Framework\Kernel\Registry;
use Rovota\Framework\Security\Encryption;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Arr;
use Throwable;

final class CookieManager
{

	/**
	 * @var Map<string, Cookie>
	 */
	protected static Map $received;

	/**
	 * @var Map<string, Cookie>
	 */
	protected static Map $queued;
	
	protected static array $plain_text = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 */
	public static function initialize(): void
	{
		self::$received = new Map();
		self::$queued = new Map();

		self::$plain_text = array_merge(Registry::array('security.plain_text_cookies'), [
			'locale', Registry::string('security.csrf.cookie_name', 'csrf_token'),
		]);

		self::loadCookiesFromPayload();

		dump(self::$received);
	}

	// -----------------

	/**
	 * @returns Map<string, Cookie>
	 */
	public static function received(): Map
	{
		return self::$received;
	}

	// -----------------

	/**
	 * @returns Map<string, Cookie>
	 */
	public static function queued(): Map
	{
		return self::$queued;
	}

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	public static function hasEncryptionEnabled(string $name): bool
	{
		return Arr::contains(self::$plain_text, $name) === false;
	}

	// -----------------

	protected static function loadCookiesFromPayload(): void
	{
		foreach ($_COOKIE as $name => $value) {
			if (str_contains($name, Registry::string('session.cookie_name', 'session'))) {
				continue;
			}

			$name = str_replace('__Secure-', '', trim($name));

			if (self::hasEncryptionEnabled($name)) {
				try {
					$value = Encryption::decryptString($value);
				} catch (Throwable) {
					continue;
				}
			}

			$cookie = new Cookie($name, $value, ['expires' => now()->addHour()], received: true);
			self::$received->set($name, $cookie);
		}
	}


}