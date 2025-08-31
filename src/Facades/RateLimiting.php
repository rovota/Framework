<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Http\Throttling\Limit;
use Rovota\Framework\Http\Throttling\Limiter;
use Rovota\Framework\Http\Throttling\LimitManager;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Facade;

/**
 * @method static Limiter|null limiter(string $name)
 * @method static Limiter define(string $name, Closure|Limit $callback)
 *
 * @method static void hit()
 * @method static void reset()
 * @method static Bucket attempts()
 * @method static Bucket remaining()
 * @method static bool tooManyAttempts()
 */
final class RateLimiting extends Facade
{

	public static function service(): LimitManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return LimitManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'limiter' => 'get',
			'define' => 'define',
			default => function (LimitManager $instance, string $method, array $parameters = []) {
				return $instance->getActiveLimiter()?->$method(...$parameters);
			},
		};
	}

}