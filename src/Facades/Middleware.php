<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Kernel\MiddlewareManager;
use Rovota\Framework\Support\Facade;

/** *
 * @method static bool has(string $name)
 * @method static void register(string $name, string $target, bool $global = false)
 * @method static void globalize(string $name)
 */
final class Middleware extends Facade
{

	public static function service(): MiddlewareManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return MiddlewareManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'has' => 'hasMiddleware',
			'register' => 'addMiddleware',
			'globalize' => 'setAsGlobal',
			default => $method,
		};
	}

}