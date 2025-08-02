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

	public array $default {
		get => $this->array('default', [
			'locale' => 'en_US',
			'timezone' => 'UTC',
		]);
	}

	public array $locales {
		get => $this->array('locales', [
			'en_US', // English (United States)
		]);
	}

}