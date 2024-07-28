<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Localization;

use DateTimeZone;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Internal;

/**
 * @internal
 */
final class Localization
{

	/**
	 * @var array<string, Language>
	 */
	protected static array $languages = [];
	protected static string $active_language;

	protected static array $timezones = [];
	protected static string $active_timezone;

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
		$config = require Internal::projectFile('config/localization.php');

		foreach ($config['locales'] as $locale) {
			self::loadLanguageUsingLocale($locale);
		}

		self::$timezones = timezone_identifiers_list();

		self::setActiveLanguage($config['default']['locale']);
		self::setActiveTimezone($config['default']['timezone']);
	}

	// -----------------
	// Languages

	public static function isActiveLanguage(string $locale): bool
	{
		return self::$active_language === $locale;
	}

	public static function setActiveLanguage(string $locale): void
	{
		if (isset(self::$languages[$locale])) {
			self::$active_language = $locale;
			self::$languages[$locale]->loadTranslations();
		}
	}

	public static function getActiveLanguage(): Language|null
	{
		return self::$languages[self::$active_language] ?? null;
	}

	public static function hasLanguage(string $locale): bool
	{
		return isset(self::$languages[$locale]);
	}

	public static function getLanguage(string $locale): Language|null
	{
		return self::$languages[$locale] ?? null;
	}

	/**
	 * @returns array<int, Language>
	 */
	public static function getLanguages(): array
	{
		return self::$languages;
	}

	/**
	 * @returns array<int, Language>
	 */
	public static function getLanguagesWithPrefix(string $prefix): array
	{
		return Bucket::from(self::$languages)->filter(function (Language $language) use ($prefix) {
			return str_starts_with($language->locale(), $prefix);
		})->toArray();
	}

	// -----------------
	// Timezones

	public static function setActiveTimezone(string $identifier): void
	{
		if (in_array($identifier, self::$timezones)) {
			self::$active_timezone = $identifier;
		}
	}

	public static function getActiveTimezone(): DateTimeZone
	{
		return timezone_open(self::$active_timezone);
	}

	public static function hasTimezone(string $identifier): bool
	{
		return in_array($identifier, self::$timezones);
	}

	public static function getTimezone(string $identifier): DateTimeZone|null
	{
		return self::hasTimezone($identifier) ? timezone_open($identifier) : null;
	}

	public static function getTimezones(): array
	{
		return self::$timezones;
	}

	public static function getTimezonesWithPrefix(string $prefix): array
	{
		return Bucket::from(self::$timezones)->filter(function ($timezone) use ($prefix) {
			return str_starts_with($timezone, $prefix);
		})->values()->toArray();
	}

	// -----------------
	// Translations

	public static function getTranslatedString(string $string): string
	{
		return trim(self::getActiveLanguage()->findTranslation($string) ?? $string);
	}

	// -----------------

	protected static function loadLanguageUsingLocale(string $locale): void
	{
		$file = Internal::projectFile('/config/locales/'.$locale.'.php');

		if (file_exists($file)) {
			self::$languages[$locale] = new Language($locale, require $file);
		}
	}

}