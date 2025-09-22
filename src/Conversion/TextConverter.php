<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\Inflector\Language;

/**
 * @internal
 */
final class TextConverter
{

	protected static Inflector $inflector;

	// -----------------

	protected function __construct()
	{
		// TODO: Turn this into a serviceprovider, loaded as the first one in the list. Additionally, add a way to have multiple inflectors, one per locale.
	}

	// -----------------

	public static function initialize(): void
	{
		// TODO: Add ability to-initialize with the user's preferred language.
		self::$inflector = InflectorFactory::createForLanguage(Language::ENGLISH)->build();
	}

	// -----------------

	public static function inflector(): Inflector
	{
		return self::$inflector;
	}

	// -----------------

	public static function toPlural(string $word, mixed $count = 2): string
	{
		if (is_countable($count) && !is_int($count)) {
			$count = count($count);
		}

		if ((int)abs($count) === 1) {
			return $word;
		}

		return self::matchCase(self::inflector()->pluralize($word), $word);
	}

	public static function toSingular(string $word): string
	{
		return self::matchCase(self::inflector()->singularize($word), $word);
	}

	// -----------------

	public static function toBytes(string $size): int
	{
		if (mb_strlen($size) === 0) {
			return 0;
		}

		$size = strtolower($size);
		$max = ltrim($size, '+');

		$max = match (true) {
			str_starts_with($max, '0x') => intval($max, 16),
			str_starts_with($max, '0') => intval($max, 8),
			default => (int)$max
		};

		switch (substr($size, -1)) {
			case 't':
				$max *= 1024;
			// no break
			case 'g':
				$max *= 1024;
			// no break
			case 'm':
				$max *= 1024;
			// no break
			case 'k':
				$max *= 1024;
		}

		return $max;
	}

	// -----------------

	/**
	 * @noinspection SpellCheckingInspection
	 */
	protected static function matchCase(string $value, string $comparison): string
	{
		$functions = ['mb_strtolower', 'mb_strtoupper', 'ucfirst', 'ucwords'];

		foreach ($functions as $function) {
			if ($function($comparison) === $comparison) {
				return $function($value);
			}
		}

		return $value;
	}

}