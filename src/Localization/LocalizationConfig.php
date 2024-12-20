<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Localization;

use Rovota\Framework\Support\Config;

/**
 * @property-read array $default
 * @property-read array $locales
 */
class LocalizationConfig extends Config
{

	protected function getDefault(): array
	{
		return $this->array('default', [
			'locale' => 'en_US',
			'timezone' => 'UTC',
		]);
	}

	protected function getLocales(): array
	{
		return $this->array('locales', [
			'en_US', // English (United States)
		]);
	}

}