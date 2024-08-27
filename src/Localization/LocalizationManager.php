<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Localization;

use DateTimeZone;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Path;

/**
 * @internal
 */
final class LocalizationManager extends ServiceProvider
{

	protected LanguageManager $language_manager;

	protected string $timezone_active;
	protected string $timezone_default;

	// -----------------

	public function __construct()
	{
		$config = require Path::toProjectFile('config/localization.php');

		$this->language_manager = new LanguageManager($config, $config['default']['locale']);

		$this->timezone_default = $config['default']['timezone'];

		$this->setCurrentTimezone($config['default']['timezone']);
	}

	// -----------------

	public function getLanguageManager(): LanguageManager
	{
		return $this->language_manager;
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

}