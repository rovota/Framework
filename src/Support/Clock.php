<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final class Clock implements ClockInterface
{
	public function now(): DateTimeImmutable
	{
		return new DateTimeImmutable();
	}

}