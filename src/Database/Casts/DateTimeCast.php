<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use DateTime;
use Rovota\Framework\Database\Interfaces\CastInterface;
use Rovota\Framework\Kernel\ExceptionHandler;
use Throwable;

final class DateTimeCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		return $value instanceof DateTime;
	}

	// -----------------

	public function toRaw(mixed $value, array $options): string
	{
		return $value->format('Y-m-d H:i:s');
	}

	public function fromRaw(mixed $value, array $options): DateTime
	{
		try {
			return new DateTime($value);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}

		return new DateTime();
	}

}