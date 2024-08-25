<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Cookie;

use Rovota\Framework\Facades\Registry;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Support\Arr;

/**
 * @internal
 */
final class CookieManager extends ServiceProvider
{
	
	protected array $plain_text = [];

	// -----------------

	public function __construct()
	{
		$this->plain_text = array_merge(Registry::array('security.plain_text_cookies'), [
			'locale', Registry::string('security.csrf.cookie_name', 'csrf_token'),
		]);
	}

	// -----------------

	public function createCookie(string $name, string $value, array $options = [], bool $received = false): CookieObject
	{
		return new CookieObject($name, $value, $options, $received);
	}

	// -----------------

	public function hasEncryptionEnabled(string $name): bool
	{
		return Arr::contains($this->plain_text, $name) === false;
	}

}