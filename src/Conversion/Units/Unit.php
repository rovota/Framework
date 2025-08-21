<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Units;

use JsonSerializable;
use Rovota\Framework\Support\Number;
use Rovota\Framework\Support\Str;
use Stringable;

abstract class Unit implements Stringable, JsonSerializable
{

	protected float|int $value;

	// -----------------

	public function __construct(float|int $value, bool $convertFromBaseUnit = false)
	{
		$this->setValue($value, $convertFromBaseUnit);
	}

	public function __toString(): string
	{
		return (string)$this->value;
	}

	public static function __callStatic(string $name, array $parameters = []): static|null
	{
		if (Str::startsWith($name, 'from')) {
			$unit = Str::lower(Str::after($name, 'from'));
			return self::from($parameters[0], $unit);
		}

		return null;
	}

	public function __call(string $name, array $parameters = []): Unit|static
	{
		if (Str::startsWith($name, 'to')) {
			$unit = Str::lower(Str::after($name, 'to'));
			return $this->to($unit);
		}

		return $this;
	}

	// -----------------

	public function jsonSerialize(): string
	{
		return (string)$this->value;
	}

	// -----------------

	public function getValue(): float|int
	{
		return $this->value;
	}

	// -----------------

	public function setValue(float|int $value, bool $convertFromBaseUnit): void
	{
		if ($convertFromBaseUnit === true) {
			$this->value = $this->fromBaseValue($value);
		} else {
			$this->value = $value;
		}
	}

	// -----------------

	public static function from(float|int $value, string $unit): static
	{
		$unit = trim($unit);
		if (str_contains($unit, '\\') === false) {
			$unit = static::UNIT_TYPE->class()::classFromIdentifier(trim($unit));
			if ($unit === null) {
				$unit = static::BASE_UNIT;
				return new $unit(0);
			}
		}

		return new $unit($value);
	}

	public function to(string $unit): self
	{
		$base = $this->toBaseUnit();

		if (str_contains($unit, '\\') === false) {
			$unit = static::UNIT_TYPE->class()::classFromIdentifier($unit);
			if ($unit === null) {
				return new $this(0);
			}
		}

		return new $unit($base->getValue(), true);
	}

	// -----------------

	public function format(int $precision = 3, bool $symbol = true, string|null $locale = null): string
	{
		$number = Number::format($this->value, $precision, $locale);

		return $symbol ? sprintf('%s %s', $number, static::SYMBOL) : $number;
	}

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value;
	}

	// -----------------

	protected function toBaseUnit(): Unit
	{
		$base = static::BASE_UNIT;

		return new $base($this->toBaseValue($this->value));
	}

}