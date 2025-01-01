<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Enums;

use Rovota\Framework\Support\Traits\EnumHelpers;

enum Priority: int
{
	use EnumHelpers;

	case High = 1;
	case Normal = 3;
	case Low = 5;

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Priority::High => 'High',
			Priority::Normal => 'Normal',
			Priority::Low => 'Low',
		};
	}

}