<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Localization;

use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Path;

/**
 * @internal
 */
final class LanguageManager
{

	/**
	 * @var Map<string, LanguageObject>
	 */
	protected Map $languages;

	protected string $locale_active;
	protected string $locale_default;

	// -----------------

	public function __construct(LocalizationConfig $config)
	{
		$this->languages = new Map();

		foreach ($config->locales as $locale) {
			$this->loadLanguageUsingLocale($locale);
		}

		$this->locale_default = $config->default['locale'];
		$this->locale_active = $config->default['locale'];

		$this->setActiveLocaleUsingRequest();
	}

	// -----------------

	public function has(string $locale): bool
	{
		return isset($this->languages[$locale]);
	}

	public function get(string $locale): LanguageObject|null
	{
		return $this->languages[$locale] ?? null;
	}

	public function active(): LanguageObject
	{
		return $this->languages[$this->locale_active];
	}

	// -----------------

	/**
	 * @returns Map<int, LanguageObject>
	 */
	public function all(): Map
	{
		return $this->languages;
	}

	/**
	 * @returns Map<int, LanguageObject>
	 */
	public function allWithPrefix(string $prefix): Map
	{
		return $this->languages->filter(function (LanguageObject $language) use ($prefix) {
			return str_starts_with($language->locale, $prefix);
		});
	}

	// -----------------

	public function getDefaultLocale(): string
	{
		return $this->locale_default;
	}

	public function setActiveLocale(string $locale): void
	{
		if (isset($this->languages[$locale])) {
			$this->locale_active = $locale;
		}
	}

	public function getActiveLocale(): string
	{
		return $this->locale_active;
	}

	// -----------------

	protected function loadLanguageUsingLocale(string $locale): void
	{
		$file = Path::toProjectFile('/config/locales/' . $locale . '.php');

		if (file_exists($file)) {
			$this->languages[$locale] = new LanguageObject($locale);
		}
	}

	protected function setActiveLocaleUsingRequest(): void
	{
		$request = RequestManager::instance()->current();
		$locales = $this->all()->keys()->toArray();

		$this->locale_active = $request->prefersLocale($locales, $this->locale_default);
	}

}