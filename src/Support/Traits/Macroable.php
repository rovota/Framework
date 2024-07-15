<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Traits;

use BadMethodCallException;
use Closure;
use Rovota\Framework\Kernel\MacroManager;

trait Macroable
{

	public static function macro(string $name, Closure $macro): void
	{
		MacroManager::register(static::class, $name, $macro);
	}

	// -----------------

	public static function __callStatic(string $name, array $parameters = []): mixed
	{
		if (!MacroManager::has(static::class, $name)) {
			throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $name));
		} else {
			$macro = MacroManager::get(static::class, $name);
			if ($macro instanceof Closure) {
				$macro = $macro->bindTo(null, static::class);
				return $macro(...$parameters);
			}
			return null;
		}
	}

	public function __call(string $name, array $parameters = []): mixed
	{
		if (!MacroManager::has(static::class, $name)) {
			throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $name));
		} else {
			$macro = MacroManager::get(static::class, $name);
			if ($macro instanceof Closure) {
				$macro = $macro->bindTo($this, static::class);
				return $macro(...$parameters);
			}
			return null;
		}
	}

}