<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Logging\Interfaces\ChannelInterface;
use Rovota\Framework\Logging\LoggingManager;
use Rovota\Framework\Support\Facade;
use Stringable;

/**
 * @method static ChannelInterface channel(string|null $name = null)
 * @method static ChannelInterface create(array $config, string|null $name = null)
 * @method static ChannelInterface stack(array $channels, string|null $name = null)
 *
 * @method static void debug(string|Stringable $message, array $context = [])
 * @method static void info(string|Stringable $message, array $context = [])
 * @method static void notice(string|Stringable $message, array $context = [])
 * @method static void warning(string|Stringable $message, array $context = [])
 * @method static void error(string|Stringable $message, array $context = [])
 * @method static void critical(string|Stringable $message, array $context = [])
 * @method static void alert(string|Stringable $message, array $context = [])
 * @method static void emergency(string|Stringable $message, array $context = [])
 */
final class Log extends Facade
{

	public static function service(): LoggingManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return LoggingManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'channel' => 'getChannel',
			'create' => 'createChannel',
			'stack' => 'createStack',
			default => function (LoggingManager $instance, string $method, array $parameters = []) {
				return $instance->getChannel()->$method(...$parameters);
			},
		};
	}

}