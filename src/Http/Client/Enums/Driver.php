<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client\Enums;

enum Driver: string
{

	case Dynamic = 'dynamic';

	// -----------------

	public static function isSupported(string $name): bool
	{
		$driver = self::tryFrom($name);

		if ($driver === null) {
			return false;
		}

		return true;
	}

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Driver::Dynamic => 'Dynamic',
		};
	}

	public function description(): string
	{
		return match ($this) {
			Driver::Dynamic => 'Use a connector generated based on your own configuration.',
		};
	}

}