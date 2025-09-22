<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

abstract class ServiceProvider
{

	public static function instance(): static
	{
		return Framework::service(static::class);
	}

}