<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use Rovota\Framework\Kernel\Registry;
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

			if (CookieManager::hasEncryptionEnabled($name)) {
				try {
					$value = EncryptionManager::getAgent()->decrypt($value, false);
				} catch (Throwable) {
					continue;
				}
			}

			$items[$name] = new Cookie($name, $value, ['expires' => now()->addHour()], received: true);
		}

		parent::__construct($items);
	}

	// -----------------

	public function recycle(string $name, string|null $value, array $options = []): void
	{
		$cookie = $this->get($name);
		if ($cookie instanceof Cookie) {
			$cookie->value = $value ?? $cookie->value;
			$cookie->update($options);
			ResponseManager::attachCookie($cookie);
		}
	}

	public function expire(string $name): void
	{
		$cookie = $this->get($name);
		if ($cookie instanceof Cookie) {
			$cookie->update(['expires' => -1]);
			ResponseManager::attachCookie($cookie);
		}
	}

}