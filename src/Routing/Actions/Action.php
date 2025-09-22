<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing\Actions;

abstract class Action
{

	protected static bool $limiter = false;

	// -----------------

	public static function name(): string
	{
		return static::$name;
	}

	public static function export(): array
	{
		return [
			'name' => static::$name,
			'method' => static::$method,
			'path' => static::$path,
			'limiter' => static::$limiter,
		];
	}

}