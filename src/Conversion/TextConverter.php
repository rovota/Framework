<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion;

final class TextConverter
{

	protected static array $accent_map = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function toAscii(string $string): string
	{
		if (empty(self::$accent_map)) {
			self::loadAccentMap();
		}
		$string = strtr($string, self::$accent_map);
		return trim($string);
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

	protected static function loadAccentMap(): void
	{
		self::$accent_map = include 'accent_map.php';
	}

}