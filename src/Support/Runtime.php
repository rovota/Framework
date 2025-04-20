<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Rovota\Framework\Conversion\Units\Time;

final class Runtime
{

	// Microseconds
	protected int $duration = 0;

	// -----------------

	public static function start(): Runtime
	{
		return new Runtime();
	}

	// -----------------

	public function duration(int $value, string $unit = 'microseconds'): Runtime
	{
		$time = Time::from($value, $unit)->to('microseconds');
		$this->duration = (int) $time->getValue();

		return $this;
	}

}