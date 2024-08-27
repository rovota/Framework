<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Localization\Middleware;

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
		$manager = LocalizationManager::instance()->getLanguageManager();

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
					CookieObject::create('locale', $query, ['expires' => now()->addYear()])
				);
			}
		}

		// TODO: Attempt to get a value from an identity
//		if (AuthManager::activeProvider()->check()) {
//			$identity = AuthManager::activeProvider()->identity();
//			if (LocalizationManager::hasLanguage($identity->getLanguage()->id)) {
//				$this->locale = $identity->getLanguage()->locale;
//			}
//			$identity_timezone = $identity->meta('timezone');
//			if ($identity_timezone !== null) {
//				LocalizationManager::setActiveTimezone($identity_timezone);
//			}
//		}

		$manager->setActiveLocale($this->locale);
	}

}