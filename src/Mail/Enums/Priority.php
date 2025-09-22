<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Enums;

enum Priority: int
{

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