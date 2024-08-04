<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Rovota\Framework\Http\RequestManager;

final class Request
{

	protected function __construct()
	{
	}

	// -----------------

	public static function current(): \Rovota\Framework\Http\Request
	{
		return RequestManager::getCurrent();
	}

}