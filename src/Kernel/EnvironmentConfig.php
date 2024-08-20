<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use Rovota\Framework\Support\Config;

/**
 * @property-read string $cookie_domain
 * @property-read array $services
 */
class EnvironmentConfig extends Config
{

	protected function getCookieDomain(): string|null
	{
		return $this->get('cookie_domain', $_SERVER['SERVER_NAME']);
	}

	// -----------------

	protected function getServices(): array
	{
		return $this->array('services');
	}

	// -----------------

	// TODO: methods like authProviders() and libraries()
	// For example, Environment::authProviders() returns an array with auth provider classes/config.
	// And Environment::libraries() returns an array of library classes to call a load() method on.

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

}