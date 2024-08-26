<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Database\CastingManager;
use Rovota\Framework\Database\Interfaces\CastInterface;
use Rovota\Framework\Support\Facade;

/**
 * @method static CastInterface|null get(string $name)
 * @method static void register(CastInterface $cast, string|null $name = null)
 *
 * @method static mixed toRaw(mixed $value, string|array $options)
 * @method static mixed toRawAutomatic(mixed $value)
 * @method static mixed fromRaw(mixed $value, string|array $options)
 *
 */
final class Cast extends Facade
{

	public static function service(): CastingManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return CastingManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'get' => 'getCast',
			'register' => 'addCast',

			'toRaw' => 'castToRaw',
			'toRawAutomatic' => 'castToRawAutomatic',
			'fromRaw' => 'castFromRaw',
			default => $method,
		};
	}

}