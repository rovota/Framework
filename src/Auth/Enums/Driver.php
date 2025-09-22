<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth\Enums;

use Rovota\Framework\Database\ConnectionManager;

enum Driver: string
{

	case Standard = 'standard';

	// -----------------

	public static function isSupported(string $name): bool
	{
		$driver = self::tryFrom($name);

		if ($driver === null) {
			return false;
		}

		if (ConnectionManager::instance()->get() === null) {
			return false;
		}

		return true;
	}

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Driver::Standard => 'Standard',
		};
	}

	public function description(): string
	{
		return match ($this) {
			Driver::Standard => 'Standard auth provider with preconfigured behavior and models.',
		};
	}

}