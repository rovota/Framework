<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use Rovota\Framework\Facades\Cookie;
use Rovota\Framework\Facades\Registry;
use Rovota\Framework\Security\EncryptionManager;
use Throwable;

class RequestCookies extends RequestData
{

	public function __construct()
	{
		$items = [];

		foreach ($_COOKIE as $name => $value) {
			if (str_contains($name, Registry::string('session.cookie_name', 'session'))) {
				continue;
			}

			$name = str_replace('__Secure-', '', trim($name));

			if (Cookie::hasEncryptionEnabled($name)) {
				try {
					$value = EncryptionManager::instance()->getAgent()->decrypt($value, false);
				} catch (Throwable) {
					continue;
				}
			}

			$items[$name] = Cookie::create($name, $value, ['expires' => now()->addHour()], received: true);
		}

		parent::__construct($items);
	}

	// -----------------

	public function recycle(string $name, string|null $value, array $options = []): void
	{
		$cookie = $this->get($name);
		if ($cookie instanceof CookieObject) {
			$cookie->value = $value ?? $cookie->value;
			$cookie->update($options);
			ResponseManager::instance()->attachCookie($cookie);
		}
	}

	public function expire(string $name): void
	{
		$cookie = $this->get($name);
		if ($cookie instanceof CookieObject) {
			$cookie->update(['expires' => -1]);
			ResponseManager::instance()->attachCookie($cookie);
		}
	}

}