<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use Rovota\Framework\Caching\CacheManager;
use Rovota\Framework\Database\CastingManager;
use Rovota\Framework\Database\ConnectionManager;
use Rovota\Framework\Http\Client\ClientManager;
use Rovota\Framework\Http\Cookie\CookieManager;
use Rovota\Framework\Http\MiddlewareManager;
use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Http\Response\ResponseManager;
use Rovota\Framework\Localization\LocalizationManager;
use Rovota\Framework\Logging\LoggingManager;
use Rovota\Framework\Routing\RouteManager;
use Rovota\Framework\Security\EncryptionManager;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Views\ViewManager;
use RuntimeException;

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
		$services = [
			// Foundation
			'registry' => RegistryManager::class,
			'logging' => LoggingManager::class,
			'cache' => CacheManager::class,
			'database' => ConnectionManager::class,
			'client' => ClientManager::class,
			'encryption' => EncryptionManager::class,
			'casting' => CastingManager::class,
			'cookie' => CookieManager::class,
			'request' => RequestManager::class,
			'localize' => LocalizationManager::class,
			'response' => ResponseManager::class,
			'views' => ViewManager::class,
			'middleware' => MiddlewareManager::class,
			'routing' => RouteManager::class,
		];

		foreach ($this->array('services') as $name => $class) {
			if (isset($services[$name])) {
				throw new RuntimeException("A service with the name '$name' already exists.");
			}
			$services[$name] = $class;
		}

		return $services;
	}

	// -----------------

	// TODO: method authProviders()
	// For example, Environment::authProviders() returns an array with auth provider classes/config.

}