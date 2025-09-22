<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Rovota\Framework\Kernel\Environment;
use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Kernel\Server;
use Rovota\Framework\Support\Facade;

final class App extends Facade
{

	public static function environment(): Environment
	{
		return Framework::environment();
	}

	// -----------------

	public static function isLocal(): bool
	{
		return App::environment()->isLocal();
	}

	public static function isTestable(): bool
	{
		return App::environment()->isTestable();
	}

	public static function isStaged(): bool
	{
		return App::environment()->isStaged();
	}

	public static function isProduction(): bool
	{
		return App::environment()->isProduction();
	}

	// -----------------

	public static function server(): Server
	{
		return Framework::environment()->server;
	}

}