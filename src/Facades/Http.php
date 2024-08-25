<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Http\Client\Client;
use Rovota\Framework\Http\Client\ClientManager;
use Rovota\Framework\Http\Client\Request;
use Rovota\Framework\Support\Facade;

/**
 * @method static Client client(mixed $options = [])
 * @method static Request request(string $method, string $location, array $options = [])
 * @method static Request get(string $location, array $options = [])
 * @method static Request delete(string $location, array $options = [])
 * @method static Request head(string $location, array $options = [])
 * @method static Request options(string $location, array $options = [])
 * @method static Request patch(string $location, array $options = [])
 * @method static Request post(string $location, array $options = [])
 * @method static Request put(string $location, array $options = [])
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
			'client' => 'createClient',
			default => function (ClientManager $instance, string $method, array $parameters = []) {
				return $instance->createClient()->$method(...$parameters);
			},
		};
	}

}