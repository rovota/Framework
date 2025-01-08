<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Request;

use Closure;
use Rovota\Framework\Http\Cookie\CookieManager;
use Rovota\Framework\Http\Cookie\CookieObject;
use Rovota\Framework\Http\Response\ResponseManager;
use Rovota\Framework\Security\EncryptionManager;
use Throwable;

class RequestCookies extends RequestData
{

	public function __construct()
	{
		$items = [];

		foreach ($_COOKIE as $name => $value) {
			$name = str_replace('__Secure-', '', trim($name));

			if (CookieManager::instance()->hasEncryptionEnabled($name)) {
				try {
					$value = EncryptionManager::instance()->getAgent()->decrypt($value, false);
				} catch (Throwable) {
					continue;
				}
			}

			$items[$name] = CookieManager::instance()->createCookie($name, $value, ['expires' => now()->addHour()], received: true);
		}

		parent::__construct($items);
	}

	// -----------------

	public function get(mixed $key, mixed $default = null): CookieObject|null
	{
		return $this->offsetGet($key) ?? ($default instanceof Closure ? $default() : $default);
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