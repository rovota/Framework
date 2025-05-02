<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use NumberFormatter;
use Rovota\Framework\Facades\Language;
use Throwable;

final class Number
{

	protected static string|null $currency = null;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function format(int|float $number, int $precision = 2, string|null $locale = null): string
	{
		$formatter = NumberFormatter::create(self::getLocale($locale), NumberFormatter::DECIMAL);
		$formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $precision);

		return $formatter->format($number);
	}

	// -----------------

	public static function setCurrency(string $value): void
	{
		self::$currency = $value;
	}

	public static function currency(int|float $amount, string|null $in = null, int $precision = 2, string|null $locale = null): string
	{
		$formatter = NumberFormatter::create(self::getLocale($locale), NumberFormatter::CURRENCY);
		$formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $precision);

		return $formatter->formatCurrency($amount, $in ?? self::$currency ?? $formatter->getSymbol(NumberFormatter::INTL_CURRENCY_SYMBOL));
	}

	public static function percentage(int|float $number, int $precision = 0, string|null $locale = null): string
	{
		$value = self::format($number, $precision, self::getLocale($locale));

		return sprintf('%s %s', $value, '%');
	}

	// -----------------

	public static function storage(int|float $bytes, int $precision = 2, string $format = 'short', string|null $locale = null): string
	{
		$locale = self::getLocale($locale);
		$suffixes = Language::get($locale)->units()->get('storage.'.$format);

		$data = self::getAbbreviationData($bytes, 1024, $suffixes);
		$value = self::format($data['value'], $precision, $locale);

		return sprintf('%s%s', $value, Str::prepend($data['suffix'], $format === 'long' ? ' ' : ''));
	}

	public static function shorten(int|float $number, int $precision = 0, string $format = 'short', string|null $locale = null): string
	{
		$locale = self::getLocale($locale);
		$suffixes = Language::get($locale)->units()->get('numbers.'.$format);

		$data = self::getAbbreviationData($number, 1000, $suffixes);
		$value = self::format($data['value'], $precision, $locale);

		return sprintf('%s%s', $value, Str::prepend($data['suffix'], $format === 'long' ? ' ' : ''));
	}

	// -----------------

	public static function random(int $length): string
	{
		$iteration = 0;
		$code = '';
		while ($iteration < $length) {
			try {
				$code .= random_int(0, 9);
			} catch (Throwable) { }
			$iteration++;
		}

		return strlen($code) < $length ? '458676' : $code;
	}

	// -----------------

	protected static function getAbbreviationData(int|float $value, int $scale, array $suffixes): array
	{
		$class = min((int) log($value, $scale), count($suffixes) - 1);

		return [
			'value' => $value / pow($scale, $class),
			'suffix' => $suffixes[$class],
		];
	}

	protected static function getLocale(string|null $locale): string
	{
		return $locale ?? Language::active()->locale ?? 'en_US';
	}

}