<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Support\Facade;
use Rovota\Framework\Views\Components\Link;
use Rovota\Framework\Views\Components\Meta;
use Rovota\Framework\Views\Components\Script;
use Rovota\Framework\Views\View;
use Rovota\Framework\Views\ViewManager;

/**
 * @method static View|null current()
 *
 * @method static Link attachLink(string $identifier, Link|array $attributes)
 * @method static Meta attachMeta(string $identifier, Meta|array $attributes)
 * @method static Script attachScript(string $identifier, Script|array $attributes)
 * @method static void attachVariable(string $identifier, mixed $value)
 */
final class Views extends Facade
{

	public static function service(): ViewManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return ViewManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return $method;
	}

}