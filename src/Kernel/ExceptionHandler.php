<?php

/**
 * @copyright   Copyright (c), Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

final class ExceptionHandler
{

	private static bool $debug_enabled = false;

	private static bool $log_enabled = false;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
//		error_reporting(getenv('ENABLE_DEBUG') === 'true' ? E_ALL : 0);
		ini_set('display_errors', getenv('ENABLE_DEBUG') === 'true' ? 1 : 0);
		ini_set('log_errors', getenv('ENABLE_LOGGING') === 'true' ? 1 : 0);
	}

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------


}