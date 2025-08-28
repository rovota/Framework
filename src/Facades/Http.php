<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Http\Client\Client;
use Rovota\Framework\Http\Client\ClientManager;
use Rovota\Framework\Http\Client\Enums\Driver;
use Rovota\Framework\Http\Client\Requests\BasicRequest;
use Rovota\Framework\Http\Client\Requests\FormRequest;
use Rovota\Framework\Http\Client\Requests\JsonRequest;
use Rovota\Framework\Support\Facade;

/**
 * @method static Client client(string|null $name = null)
 * @method static Client|null clientWithDriver(Driver $driver)
 * @method static Client create(array $config, string|null $name = null)
 *
 * @method static BasicRequest request(string $endpoint, string $method)
 *
 * @method static JsonRequest json(string $endpoint, string $method = 'POST')
 * @method static FormRequest form(string $endpoint, string $method = 'POST')
 *
 * @method static BasicRequest get(string $endpoint)
 * @method static BasicRequest delete(string $endpoint)
 * @method static BasicRequest head(string $endpoint)
 * @method static BasicRequest options(string $endpoint)
 * @method static BasicRequest patch(string $endpoint)
 * @method static BasicRequest post(string $endpoint)
 * @method static BasicRequest put(string $endpoint)
 */
final class Http extends Facade
{

	public static function service(): ClientManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return ClientManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'client' => 'get',
			'clientWithDriver' => 'getWithDriver',
			'create' => 'createClient',
			default => function (ClientManager $instance, string $method, array $parameters = []) {
				return $instance->get()->$method(...$parameters);
			},
		};
	}

}