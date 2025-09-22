<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Traits;

use BadMethodCallException;
use Closure;
use ReflectionClass;

trait MagicMethods
{

	public static function __callStatic(string $name, array $parameters = []): mixed
	{
		if (static::isMacroable() && isset(static::$macros[$name])) {
			$macro = static::$macros[$name];
			if ($macro instanceof Closure) {
				$macro = $macro->bindTo(null, static::class);
				return $macro(...$parameters);
			}
		}

		if (static::isQueryable()) {
			if (str_starts_with($name, 'where')) {
				$builder = static::getQueryBuilderFromStaticModel();
				return $builder->select()->{$name}(...$parameters);
			}
		}

		throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $name));
	}

	public function __call(string $name, array $parameters = []): mixed
	{
		if (static::isMacroable() && isset(static::$macros[$name])) {
			$macro = static::$macros[$name];
			if ($macro instanceof Closure) {
				$macro = $macro->bindTo($this, static::class);
				return $macro(...$parameters);
			}
		}

		throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $name));
	}

	// -----------------

	public static function isMacroable(): bool
	{
		return static::getReflectionClass()->hasMethod('macro');
	}

	public static function isQueryable(): bool
	{
		return static::getReflectionClass()->hasMethod('getQueryBuilderFromStaticModel');
	}

	// -----------------

	protected static function getReflectionClass(): ReflectionClass
	{
		return new ReflectionClass(static::class);
	}

}