<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security;

use Rovota\Framework\Conversion\Units\Time;
use Rovota\Framework\Kernel\ExceptionHandler;
use Throwable;

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
		$this->duration = (int)$time->getValue();

		return $this;
	}

	// -----------------

	public function execute(callable $callback): mixed
	{
		$result = null;

		$start = microtime(true);

		try {
			$result = $callback($this);
		} catch (Throwable $throwable) {
			ExceptionHandler::handleThrowable($throwable);
		}

		$remainder = intval($this->duration - ((microtime(true) - $start) * 1000000));

		if ($remainder > 0) {
			usleep($remainder);
		}

		return $result;
	}

}