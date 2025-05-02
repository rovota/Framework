<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Identity\Enums;

enum SuspensionType: string
{

	case Automatic = 'automatic';
	case Manual = 'manual';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			SuspensionType::Automatic => 'Automatic',
			SuspensionType::Manual => 'Manual',
		};
	}

}