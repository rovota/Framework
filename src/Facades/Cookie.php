<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Http\CookieInstance;
use Rovota\Framework\Http\CookieManager;
use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Support\Facade;

/**
 * @method static CookieInstance create(string $name, string $value, array $options = [], bool $received = false)
 * @method static string domain()
 * @method static bool hasEncryptionEnabled(string $name)
 */
final class Cookie extends Facade
{

	public static function service(): CookieManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return CookieManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'create' => 'createCookie',
			'domain' => Framework::environment()->config()->cookie_domain,
			default => $method,
		};
	}

}