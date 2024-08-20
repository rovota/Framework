<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Closure;
use Rovota\Framework\Kernel\Framework;
use RuntimeException;

abstract class Facade
{

	public static function __callStatic($method, $parameters)
	{
		$instance = self::getFacadeTargetInstance();

		if ($instance === null) {
			throw new RuntimeException("A target for method '{$method}' on " . static::class . ' could not be resolved.');
		}

		$target = static::getMethodTarget($method);

		if ($target instanceof Closure) {
			return $target($instance, $method, $parameters);
		}

		return $instance->$target(...$parameters);
	}

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function service(): object|null
	{
		return self::getFacadeTargetInstance();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return Str::afterLast(Str::lower(static::class), '\\');
	}

	protected static function getMethodTarget(string $method): mixed
	{
		return $method;
	}

	// -----------------

	protected static function getFacadeTargetInstance(): object|null
	{
		return Framework::service(static::getFacadeTarget());
	}

}