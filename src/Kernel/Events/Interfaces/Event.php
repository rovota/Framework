<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Events\Interfaces;

interface Event
{

	public static function dispatch(...$data): void;

}