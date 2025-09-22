<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Throttling\Enums;

enum PeriodType: string
{

	case Second = 'second';
	case Minute = 'minute';
	case Hour = 'hour';
	case Day = 'day';
	case Week = 'week';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			PeriodType::Second => 'Per second',
			PeriodType::Minute => 'Per minute',
			PeriodType::Hour => 'Per hour',
			PeriodType::Day => 'Per day',
			PeriodType::Week => 'Per week',
		};
	}

}