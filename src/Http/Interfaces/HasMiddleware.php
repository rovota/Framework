<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Interfaces;

interface HasMiddleware
{

	public static function middleware(): array;

}