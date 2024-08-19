<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use RuntimeException;

abstract class Facade
{

	abstract protected static function getFacadeTarget(): string;

	protected static function getFacadeTargetInstance(): object|null
	{
		return Framework::services()->get(static::getFacadeTarget());
	}

	// -----------------

	public static function __callStatic($method, $parameters)
	{
		$instance = self::getFacadeTargetInstance();

		if ($instance === null) {
			throw new RuntimeException('A target for this method could not be found.');
		}

		return $instance->$method(...$parameters);
	}

}