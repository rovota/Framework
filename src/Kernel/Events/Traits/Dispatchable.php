<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Events\Traits;

use Rovota\Framework\Kernel\Events\EventManager;

trait Dispatchable
{

	public static function dispatch(...$data): void
	{
		EventManager::instance()->dispatchEvent(new static(...$data));
	}

}