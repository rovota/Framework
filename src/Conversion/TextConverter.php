<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\Inflector\Language;

final class TextConverter
{

	protected static Inflector $inflector;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		self::$inflector = InflectorFactory::createForLanguage(Language::ENGLISH)->build();
	}

	// -----------------

	public static function inflector(): Inflector
	{
		return self::$inflector;
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

}