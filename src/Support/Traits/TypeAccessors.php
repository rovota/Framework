<?php

/**
 * @copyright   Léandro Tijink
 * * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Support\Traits;

use BackedEnum;
use DateTime;
use DateTimeZone;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Support\Text;
use Throwable;

trait TypeAccessors
{

	public function array(string $key, array $default = []): array
	{
		$value = $this->get($key);
		return is_array($value) ? $value : (mb_strlen($value ?? '') > 0 ? explode(',', $value ?? '') : $default);
	}

	public function bool(string $key, bool $default = false): bool
	{
		return filter_var($this->get($key, $default), FILTER_VALIDATE_BOOLEAN);
	}

	public function date(string $key, DateTimeZone|null $timezone = null): DateTime|null
	{
		try {
			return $this->has($key) ? new DateTime($this->string($key), $timezone) : null;
		} catch (Throwable $throwable) {
			ExceptionHandler::handleThrowable($throwable);
		}
		return null;
	}

	public function enum(string $key, BackedEnum|string $class, BackedEnum|null $default = null): BackedEnum|null
	{
		$value = $this->get($key);

		if ($value !== null) {
			return ($value instanceof BackedEnum === false) ? $class::TryFrom($value) : $value;
		}

		return $default;
	}

	public function float(string $key, float $default = 0.00): float|false
	{
		return filter_var($this->get($key, $default), FILTER_VALIDATE_FLOAT);
	}

	public function int(string $key, int $default = 0): int|false
	{
		return filter_var($this->get($key, $default), FILTER_VALIDATE_INT);
	}

	public function string(string $key, string $default = ''): string
	{
		return (string)$this->get($key, $default);
	}

	// -----------------

	public function text(string $key, Text $default = new Text()): Text
	{
		return new Text($this->get($key, $default));
	}

	public function moment(string $key, mixed $default = null, DateTimeZone|int|string|null $timezone = null): Moment|null
	{
		return new Moment($this->get($key, $default), $timezone);
	}

}