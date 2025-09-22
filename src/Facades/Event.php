<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Kernel\Events\EventListener;
use Rovota\Framework\Kernel\Events\EventManager;
use Rovota\Framework\Support\Facade;

/**
 * @method static EventListener listen(string $event, EventListener|string $listener)
 */
final class Event extends Facade
{

	public static function service(): EventManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return EventManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'listen' => 'addListener',
			default => function (EventManager $instance, string $method, array $parameters = []) {
				return $instance->$method(...$parameters);
			},
		};
	}

}