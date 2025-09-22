<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security;

use Rovota\Framework\Facades\Registry;
use Rovota\Framework\Http\Cookie\CookieObject;
use Rovota\Framework\Support\Str;

final class CsrfManager
{

	protected static string $token;

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
		self::initializeCsrfToken();
	}

	// -----------------

	public static function getToken(): string
	{
		return self::$token;
	}

	public static function getTokenName(): string
	{
		return Registry::string('security.csrf.cookie_name', 'csrf_protection_token');
	}

	// -----------------
	// Internal

	protected static function initializeCsrfToken(): void
	{
		$cookie = request()->cookies->get(self::getTokenName());

		if ($cookie instanceof CookieObject) {
			self::$token = $cookie->value;
			return;
		}

		self::$token = Str::random(80);
	}


}