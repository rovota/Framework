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
use Rovota\Framework\Kernel\Events\EventManager;
use Rovota\Framework\Localization\LocalizationManager;
use Rovota\Framework\Logging\LoggingManager;
use Rovota\Framework\Mail\MailManager;
use Rovota\Framework\Routing\RouteManager;
use Rovota\Framework\Security\EncryptionManager;
use Rovota\Framework\Storage\StorageManager;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Views\ViewManager;
use RuntimeException;

class EnvironmentConfig extends Config
{

	public string $cookie_domain {
		get => $this->get('cookie_domain', $_SERVER['SERVER_NAME']);
		set {
			$this->set('cookie_domain', trim($value));
		}
	}

	// -----------------

	public array $services {
		get {
			$services = [
				// Foundation
				'registry' => RegistryManager::class,
				'logging' => LoggingManager::class,
				'cache' => CacheManager::class,
				'events' => EventManager::class,
				'database' => ConnectionManager::class,
				'storage' => StorageManager::class,
				'client' => ClientManager::class,
				'encryption' => EncryptionManager::class,
				'casting' => CastingManager::class,
				'cookie' => CookieManager::class,
				'request' => RequestManager::class,
				'localize' => LocalizationManager::class,
				'mail' => MailManager::class,
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
		set {
			$this->set('services', $value);
		}
	}

	// -----------------

	// TODO: method authProviders()
	// For example, Environment::authProviders() returns an array with auth provider classes/config.

}