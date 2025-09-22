<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Localization\Middleware;

use Rovota\Framework\Auth\AuthManager;
use Rovota\Framework\Facades\Cookie;
use Rovota\Framework\Http\Cookie\CookieObject;
use Rovota\Framework\Http\Request\RequestObject;
use Rovota\Framework\Http\Response\ResponseManager;
use Rovota\Framework\Localization\LocalizationManager;
use Rovota\Framework\Support\Str;

class DetermineLanguage
{

	protected string|null $locale = null;

	// -----------------

	public function handle(RequestObject $request): void
	{
		$manager = LocalizationManager::instance()->language_manager;

		// Attempt to get a value from a cookie
		if ($request->cookies->has('locale')) {
			$cookie = $request->cookies->get('locale');
			if ($cookie instanceof CookieObject && $manager->has($cookie->value)) {
				$this->locale = $cookie->value;
			}
		}

		// Attempt to get a value from a query parameter
		if ($request->query->has('locale') && Str::contains($request->referrer() ?? '', $request->targetHost())) {
			$query = trim($request->query->get('locale'));
			if (strlen($query) > 0 && $manager->has($query)) {
				$this->locale = $query;
				ResponseManager::instance()->attachCookie(
					Cookie::create('locale', $query, ['expires' => now()->addYear()])
				);
			}
		}

		// Attempt to get a value from the user's identity
		if (AuthManager::instance()->all()->count() > 0 && AuthManager::instance()->get()->check()) {
			$user = AuthManager::instance()->get()->user();
			if ($manager->has($user->language->locale)) {
				$this->locale = $user->language->locale;
			}
			$identity_timezone = $user->meta('timezone');
			if ($identity_timezone !== null) {
				LocalizationManager::instance()->setCurrentTimezone($identity_timezone);
			}
		}

		if ($this->locale !== null) {
			$manager->setActiveLocale($this->locale);
		}
	}

}