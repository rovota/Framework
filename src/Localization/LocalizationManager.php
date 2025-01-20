<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Localization;

use DateTimeZone;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Structures\Bucket;

/**
 * @internal
 */
final class LocalizationManager extends ServiceProvider
{

	protected LocalizationConfig $config;

	public LanguageManager $language_manager {
		get {
			return $this->language_manager;
		}
	}

	protected string $timezone_active;
	protected string $timezone_default;

	// -----------------

	public function __construct()
	{
		$this->config = LocalizationConfig::load('config/localization');

		$this->language_manager = new LanguageManager($this->config);

		$this->timezone_default = $this->config->default['timezone'];
		$this->setCurrentTimezone($this->config->default['timezone']);
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