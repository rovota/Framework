<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Enums;

enum Driver: string
{

	case SMTP = 'smtp';

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
		};
	}

	public function description(): string
	{
		return match ($this) {
			Driver::SMTP => 'Use the SMTP protocol to send your email.',
		};
	}

}