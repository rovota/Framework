<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Closure;
use Rovota\Framework\Conversion\TextConverter;
use Throwable;

final class Str
{

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * Determines whether a string is empty.
	 */
	public static function isEmpty(string $string): bool
	{
		return self::length(trim($string)) === 0;
	}

	/**
	 * Determines whether a string is formatted as a slug.
	 */
	public static function isSlug(string $string, string|null $separator = null): bool
	{
		return $string === self::slug($string, $separator);
	}

	/**
	 * Determines whether a string only contains ASCII characters.
	 */
	public static function isAscii(string $string): bool
	{
		return $string === TextConverter::inflector()->unaccent($string);
	}

	// -----------------

	/**
	 * Transforms the given string into uppercase.
	 */
	public static function upper(string $string): string
	{
		return mb_convert_case($string, MB_CASE_UPPER, 'UTF-8');
	}

	/**
	 * Transforms the given string into lowercase.
	 */
	public static function lower(string $string): string
	{
		return mb_convert_case($string, MB_CASE_LOWER, 'UTF-8');
	}

	/**
	 * Transforms the given string into title case.
	 */
	public static function title(string $string): string
	{
		return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
	}

	// -----------------

	/**
	 * Replaces non-ASCII characters with their ASCII equivalent.
	 */
	public static function simplify(string $string): string
	{
		return TextConverter::inflector()->unaccent($string);
	}

	/**
	 * Adds a separator (underscore by default), while transforming the string into lowercase.
	 */
	public static function snake(string $string, string $separator = '_'): string
	{
		preg_match_all('!([A-Z][A-Z\d]*(?=$|[A-Z][a-z\d])|[A-Za-z][a-z\d]+)!', $string, $matches);
		$ret = $matches[0];
		foreach ($ret as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}
		return implode($separator, $ret);
	}

	/**
	 * Adds a dash as separator, while transforming the string into lowercase.
	 */
	public static function kebab(string $string): string
	{
		return self::snake($string, '-');
	}

	/**
	 * Removes spaces, underscores and dashes, while transforming the first letter of each word into uppercase.
	 */
	public static function pascal(string $string): string
	{
		return str_replace([' ', '_', '-'], '', ucwords($string, ' _-'));
	}

	/**
	 * Transforms the given string into camel case.
	 */
	public static function camel(string $string): string
	{
		return lcfirst(self::pascal($string));
	}

	/**
	 * Simplifies the given string and removes special characters, making the string suitable for URL usage.
	 */
	public static function slug(string $string, string $separator = '-'): string
	{
		return str_replace('-', $separator, TextConverter::inflector()->urlize($string));
	}

	// -----------------

	public static function plural(string $string, mixed $count): string
	{
		return TextConverter::toPlural($string, $count);
	}

	public static function singular(string $string): string
	{
		return TextConverter::toSingular($string);
	}

	// -----------------

	public static function prepend(string $string, string $addition): string
	{
		return $addition.$string;
	}

	public static function append(string $string, string $addition): string
	{
		return $string.$addition;
	}

	public static function wrap(string $string, string $start, string|null $end = null): string
	{
		return $start.$string.($end ?? $start);
	}

	public static function start(string $string, string $value): string
	{
		return str_starts_with($string, $value) ? $string : self::prepend($string, $value);
	}

	public static function finish(string $string, string $value): string
	{
		return str_ends_with($string, $value) ? $string : self::append($string, $value);
	}

	public static function startAndFinish(string $string, string $value): string
	{
		return self::start(self::finish($string, $value), $value);
	}

	public static function padLeft(string $string, int $length, string $pad_with = ' '): string
	{
		// Inspired by the Laravel Str::padLeft() method.
		$space = max(0, $length - mb_strlen($string));
		return mb_substr(str_repeat($pad_with, $space), 0, $space).$string;
	}

	public static function padRight(string $string, int $length, string $pad_with = ' '): string
	{
		// Inspired by the Laravel Str::padRight() method.
		$space = max(0, $length - mb_strlen($string));
		return $string.mb_substr(str_repeat($pad_with, $space), 0, $space);
	}

	public static function padBoth(string $string, int $length, string $pad_with = ' '): string
	{
		// Inspired by the Laravel Str::padBoth() method.
		$space = max(0, $length - mb_strlen($string));
		$padding_left = mb_substr(str_repeat($pad_with, floor($space / 2)), 0, floor($space / 2));
		$padding_right = mb_substr(str_repeat($pad_with, ceil($space / 2)), 0, ceil($space / 2));
		return $padding_left.$string.$padding_right;
	}

	// -----------------

	public static function shuffle(string $string): string
	{
		return str_shuffle($string);
	}

	public static function reverse(string $string): string
	{
		return implode(array_reverse(mb_str_split($string)));
	}

	public static function scramble(string $string): string
	{
		$words = explode(' ', $string);
		$string = '';

		foreach ($words as $word) {
			if (mb_strlen($word) < 4) {
				$string .= $word.' ';
			} else {
				$string .= sprintf('%s%s%s ', $word[0], str_shuffle(substr($word, 1, -1)), $word[mb_strlen($word) - 1]);
			}
		}

		return trim($string);
	}

	public static function mask(string $string, string $replacement, int|null $length = null, int $start = 0): string
	{
		// Inspired by the Laravel Str:mask() method.
		if ($replacement === '') {
			return $string;
		}

		$segment = mb_substr($string, $start, $length, 'UTF-8');
		if ($segment === '') {
			return $string;
		}

		$str_length = mb_strlen($string, 'UTF-8');
		$start_index = $start;

		if ($start < 0) {
			$start_index = $start < -$str_length ? 0 : $str_length + $start;
		}

		$start = mb_substr($string, 0, $start_index, 'UTF-8');
		$segment_length = mb_strlen($segment, 'UTF-8');
		$end = mb_substr($string, $start_index + $segment_length);

		return $start.str_repeat(mb_substr($replacement, 0, 1, 'UTF-8'), $segment_length).$end;
	}

	public static function maskEmail(string $string, string $replacement, int $preserve = 3): string
	{
		$maskable = Str::before($string, '@');
		$rest = str_replace($maskable, '', $string);

		return Str::mask($maskable, $replacement, mb_strlen($maskable) - $preserve, $preserve).$rest;
	}

	// -----------------

	public static function length(string $string): int
	{
		return mb_strwidth($string, 'UTF-8');
	}

	public static function limit(string $string, int $length, int $start = 0, string $marker = ''): string
	{
		if (mb_strwidth($string, 'UTF-8') <= abs($length)) {
			return $string;
		} else {
			return mb_strimwidth($string, $start, $length, $marker, 'UTF-8');
		}
	}

	public static function trim(string $string, string|null $characters = null): string
	{
		return $characters !== null ? trim($string, $characters) : trim($string);
	}

	public static function trimEnd(string $string, string|null $characters = null): string
	{
		return $characters !== null ? rtrim($string, $characters) : rtrim($string);
	}

	public static function trimStart(string $string, string|null $characters = null): string
	{
		return $characters !== null ? ltrim($string, $characters) : ltrim($string);
	}

	public static function remove(string $string, string|array $values, bool $ignore_case = false): string
	{
		foreach (is_string($values) ? [$values] : $values as $value) {
			$string = ($ignore_case) ? str_ireplace($value, '', $string) : str_replace($value, '', $string);
		}
		return $string;
	}

	// -----------------

	public static function before(string $string, string $target): string
	{
		return str_contains($string, $target) ? explode($target, $string, 2)[0] : $string;
	}

	public static function beforeLast(string $string, string $target): string
	{
		return str_contains($string, $target) ? substr($string, 0, strrpos($string, $target)) : $string;
	}

	public static function after(string $string, string $target): string
	{
		return str_contains($string, $target) ? explode($target, $string, 2)[1] : $string;
	}

	public static function afterLast(string $string, string $target): string
	{
		if (str_contains($string, $target)) {
			$result = explode($target, $string);
			return end($result);
		}
		return $string;
	}

	public static function between(string $string, string $start, string $end): string
	{
		if (self::contains($string, [$start, $end])) {
			$string = self::after($string, $start);
			return self::beforeLast($string, $end);
		}
		return $string;
	}

	// -----------------

	public static function contains(string $string, mixed $needle): bool
	{
		$needles = is_array($needle) ? $needle : [$needle];

		foreach ($needles as $needle) {
			if ($needle instanceof Closure) {
				if ($needle($string) === false) {
					return false;
				}
			} else {
				if (str_contains($string, $needle) === false) {
					return false;
				}
			}
		}
		return true;
	}

	public static function containsAny(string $string, array $needles): bool
	{
		foreach ($needles as $needle) {
			if (str_contains($string, $needle)) {
				return true;
			}
		}
		return false;
	}

	public static function containsNone(string $string, array $needles): bool
	{
		foreach ($needles as $needle) {
			if (str_contains($string, $needle)) {
				return false;
			}
		}
		return true;
	}

	public static function startsWith(string $string, string $needle): bool
	{
		return str_starts_with($string, $needle);
	}

	public static function startsWithAny(string $string, array $needles): bool
	{
		foreach ($needles as $needle) {
			if (str_starts_with($string, $needle)) {
				return true;
			}
		}
		return false;
	}

	public static function startsWithNone(string $string, array $needles): bool
	{
		foreach ($needles as $needle) {
			if (str_starts_with($string, $needle)) {
				return false;
			}
		}
		return true;
	}

	public static function endsWith(string $string, string $needle): bool
	{
		return str_ends_with($string, $needle);
	}

	public static function endsWithAny(string $string, array $needles): bool
	{
		foreach ($needles as $needle) {
			if (str_ends_with($string, $needle)) {
				return true;
			}
		}
		return false;
	}

	public static function endsWithNone(string $string, array $needles): bool
	{
		foreach ($needles as $needle) {
			if (str_ends_with($string, $needle)) {
				return false;
			}
		}
		return true;
	}

	// -----------------

	public static function increment(string $string, string $separator = '-', int $step = 1): string
	{
		$matches = null;
		preg_match('/(.+)' . preg_quote($separator, '/') . '(\d+)$/', $string, $matches);

		if (isset($matches[2])) {
			$new_value = (int) $matches[2] + max($step, 0);
			return $matches[1].$separator.$new_value;
		}

		return $string.$separator.$step;
	}

	public static function decrement(string $string, string $separator = '-', int $step = 1): string
	{
		$matches = null;
		preg_match('/(.+)' . preg_quote($separator, '/') . '(\d+)$/', $string, $matches);

		if (isset($matches[2])) {
			$new_value = max((int) $matches[2] - max($step, 0), 0);
			return $new_value === 0 ? $matches[1] : $matches[1].$separator.$new_value;
		}

		return $string;
	}

	// -----------------

	public static function insert(string $string, int $interval, string $character): string
	{
		return implode($character, mb_str_split($string, $interval));
	}

	public static function merge(string $string, string|array $values): string
	{
		if (is_string($values)) {
			$values = [$values];
		}
		return empty($values) === false ? sprintf($string, ...$values) : $string;
	}

	public static function swap(string $string, array $map): string
	{
		return strtr($string, $map);
	}

	// -----------------

	public static function take(string $string, int $amount): string
	{
		return Str::limit($string, $amount);
	}

	public static function takeLast(string $string, int $amount): string
	{
		return Str::limit($string, $amount, -$amount);
	}

	// -----------------

	public static function replace(string $string, string|array $targets, string|array $values): string
	{
		return str_replace($targets, $values, $string);
	}

	public static function replaceSequential(string $string, string $target, array $values): string
	{
		$string = str_replace($target, '%s', $string);
		return sprintf($string, ...$values);
	}

	public static function replaceFirst(string $string, string $target, string $value): string
	{
		$position = strpos($string, $target);
		if ($position !== false) {
			return substr_replace($string, $value, $position, mb_strlen($target));
		}
		return $string;
	}

	public static function replaceLast(string $string, string $target, string $value): string
	{
		$position = strrpos($string, $target);
		if ($position !== false) {
			return substr_replace($string, $value, $position, mb_strlen($target));
		}
		return $string;
	}

	// -----------------

	public static function scan(string $string, string $format): array
	{
		return sscanf($string, $format);
	}

	public static function occurrences(string $string, mixed $needle): int
	{
		return max(count(explode((string)$needle, $string)) - 1, 0);
	}

	public static function wordCount(string $string): int
	{
		return str_word_count($string);
	}

	// -----------------

	public static function explode(string $string, string $char, int $elements = PHP_INT_MAX): array
	{
		return explode($char, $string, $elements);
	}

	public static function escape(string|null $string, string $encoding = 'UTF-8'): string|null
	{
		return $string === null ? null : htmlentities($string, encoding: $encoding);
	}

	public static function random(int $length): string
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			try {
				$randomString .= $characters[random_int(0, $charactersLength - 1)];
			} catch (Throwable) {
				continue;
			}
		}

		return $randomString;
	}

	public static function acronym(string $string, string $delimiter = '.'): string
	{
		if (empty($string)) return '';

		$acronym = '';
		foreach (preg_split('/[^\p{L}]+/u', trim($string)) as $word) {
			if(strlen($word > 0)){
				$first_letter = mb_substr($word, 0, 1);

				// Only words starting with an uppercase letter should be included.
				if ($first_letter === mb_convert_case($first_letter, MB_CASE_UPPER, 'UTF-8')) {
					$acronym .= $first_letter . $delimiter;
				}
			}
		}

		return $acronym;
	}

	// -----------------

	// TODO: toMoment() method, with optional format indication and timezone.

}