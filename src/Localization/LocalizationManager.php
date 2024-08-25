<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Localization;

use DateTimeZone;
use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Internal;

/**
 * @internal
 */
final class LocalizationManager extends ServiceProvider
{

	/**
	 * @var array<string, LanguageObject>
	 */
	protected array $languages = [];

	protected string $locale_active;
	protected string $locale_default;

	protected string $timezone_active;
	protected string $timezone_default;

	// -----------------

	public function __construct()
	{
		$config = require Internal::projectFile('config/localization.php');

		foreach ($config['locales'] as $locale) {
			$this->loadLanguageUsingLocale($locale);
		}

		$this->locale_default =$config['default']['locale'];
		$this->timezone_default =$config['default']['timezone'];

		$this->determineCurrentLanguage($config['default']['locale']);
		$this->setCurrentTimezone($config['default']['timezone']);
	}

	// -----------------
	// Languages

	public function getCurrentLanguage(): LanguageObject
	{
		return $this->languages[$this->locale_active];
	}

	public function hasLanguage(string $locale): bool
	{
		return isset($this->languages[$locale]);
	}

	public function getLanguage(string $locale): LanguageObject|null
	{
		return $this->languages[$locale] ?? null;
	}

	/**
	 * @returns array<int, LanguageObject>
	 */
	public function getLanguages(): array
	{
		return $this->languages;
	}

	/**
	 * @returns array<int, LanguageObject>
	 */
	public function getLanguagesWithPrefix(string $prefix): array
	{
		return Bucket::from($this->languages)->filter(function (LanguageObject $language) use ($prefix) {
			return str_starts_with($language->locale, $prefix);
		})->toArray();
	}

	// -----------------
	// Locales

	public function getDefaultLocale(): string
	{
		return $this->locale_default;
	}

	public function getActiveLocale(): string
	{
		return $this->locale_active;
	}

	public function setActiveLocale(string $locale): void
	{
		if (isset($this->languages[$locale])) {
			$this->locale_active = $locale;
		}
	}

	// -----------------
	// Timezones

	public function setCurrentTimezone(string $identifier): void
	{
		if (in_array($identifier, DateTimeZone::listIdentifiers())) {
			$this->timezone_active = $identifier;
		}
	}

	public function getCurrentTimezone(): DateTimeZone
	{
		return timezone_open($this->timezone_active);
	}

	public function getDefaultTimezone(): DateTimeZone
	{
		return timezone_open($this->timezone_default);
	}

	public function hasTimezone(string $identifier): bool
	{
		return in_array($identifier, DateTimeZone::listIdentifiers());
	}

	public function getTimezone(string $identifier): DateTimeZone|null
	{
		return $this->hasTimezone($identifier) ? timezone_open($identifier) : null;
	}

	public function getTimezones(): array
	{
		return DateTimeZone::listIdentifiers();
	}

	public function getTimezonesWithPrefix(string $prefix): array
	{
		return Bucket::from(DateTimeZone::listIdentifiers())->filter(function ($timezone) use ($prefix) {
			return str_starts_with($timezone, $prefix);
		})->values()->toArray();
	}

	// -----------------
	// Translations

	public function getTranslatedString(string $string): string
	{
		return trim($this->getCurrentLanguage()->findTranslation($string) ?? $string);
	}

	// -----------------

	protected function loadLanguageUsingLocale(string $locale): void
	{
		$file = Internal::projectFile('/config/locales/'.$locale.'.php');

		if (file_exists($file)) {
			$this->languages[$locale] = new LanguageObject($locale);
		}
	}

	protected function determineCurrentLanguage(string $default): void
	{
		$locales = array_keys($this->languages);
		$preferred = RequestManager::instance()->getCurrent()->prefersLocale($locales, $default);

		$this->setActiveLocale($preferred);
	}

}