<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Enums;

enum Driver: string
{

	case APCu = 'apcu';
	case Memory = 'array';
	case Redis = 'redis';
	case Session = 'session';

	// -----------------

	public static function isSupported(string $name): bool
	{
		$driver = self::tryFrom($name);

		if ($driver === null) {
			return false;
		}

		if ($driver === Driver::APCu) {
			if (function_exists('apcu_enabled') === false || apcu_enabled() === false) {
				return false;
			}
		}

		if ($driver === Driver::Redis) {
			if (class_exists('\Redis', false) === false) {
				return false;
			}
		}

		return true;
	}

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Driver::APCu => 'APCu',
			Driver::Memory => 'Array',
			Driver::Redis => 'Redis',
			Driver::Session => 'Session',
		};
	}

	public function description(): string
	{
		return match ($this) {
			Driver::APCu => 'Use the APCu extension with a built-in server.',
			Driver::Memory => 'Use a regular array that does not remember data.',
			Driver::Redis => 'Use a configured Redis server.',
			Driver::Session => 'Use the built-in PHP session.',
		};
	}

}