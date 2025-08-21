<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

final class Buffer
{

	protected function __construct()
	{
	}

	// -----------------

	public static function start(): void
	{
		ob_start();
	}

	public static function end(): void
	{
		if (ob_get_length() > 0) {
			ob_end_clean();
		}
	}

	// -----------------

	public static function erase(): void
	{
		if (ob_get_length() > 0) {
			ob_clean();
		}
	}

	// -----------------

	public static function retrieve(): string|null
	{
		$result = ob_get_contents();
		return $result === false ? null : $result;
	}

	public static function retrieveAndErase(): string|null
	{
		$result = ob_get_clean();
		return $result === false ? null : $result;
	}

	// -----------------

	public static function size(): int
	{
		$size = ob_get_clean();
		return $size === false ? 0 : $size;
	}

}