<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Throttling\Enums;

enum IdentifierType: string
{

	case Global = 'global';
	case Custom = 'custom';
	case IP = 'ip';
	case Token = 'token';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			IdentifierType::Global => 'Global',
			IdentifierType::Custom => 'Custom',
			IdentifierType::IP => 'IP Address',
			IdentifierType::Token => 'Token',
		};
	}

}