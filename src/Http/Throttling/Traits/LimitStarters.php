<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Throttling\Traits;

use Rovota\Framework\Http\Throttling\Enums\PeriodType;
use Rovota\Framework\Http\Throttling\Limit;

trait LimitStarters
{

	public static function none(): Limit
	{
		return new Limit();
	}

	// -----------------

	public static function perSecond(int $amount): Limit
	{
		$instance = new Limit($amount);
		$instance->config->period_type = PeriodType::Second;

		return $instance;
	}

	public static function perMinute(int $amount): Limit
	{
		$instance = new Limit($amount);
		$instance->config->period_type = PeriodType::Minute;

		return $instance;
	}

	public static function perHour(int $amount): Limit
	{
		$instance = new Limit($amount);
		$instance->config->period_type = PeriodType::Hour;

		return $instance;
	}

	public static function perDay(int $amount): Limit
	{
		$instance = new Limit($amount);
		$instance->config->period_type = PeriodType::Day;

		return $instance;
	}

	public static function perWeek(int $amount): Limit
	{
		$instance = new Limit($amount);
		$instance->config->period_type = PeriodType::Week;

		return $instance;
	}

}