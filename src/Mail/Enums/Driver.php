<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Enums;

use Rovota\Framework\Support\Traits\EnumHelpers;

enum Driver: string
{
	use EnumHelpers;

	case SMTP = 'smtp';
	case Basic = 'mail';

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
			Driver::SMTP => 'SMTP',
			Driver::Basic => 'Mail',
		};
	}

	public function description(): string
	{
		return match ($this) {
			Driver::SMTP => 'Use the SMTP protocol to send your email.',
			Driver::Basic => 'Use the built-in PHP mail functionality.',
		};
	}

}