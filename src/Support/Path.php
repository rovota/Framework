<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

final class Path
{

	protected function __construct()
	{
	}

	// -----------------

	public static function buildUsingContext(string $path, array $context): string
	{
		if (empty($context) === false) {
			if (array_is_list($context)) {
				$path = preg_replace('/{(.*?)}/', '{item}', $path);
				$path = Str::replaceSequential($path, '{item}', $context);
			} else {
				foreach ($context as $key => $value) {
					$path = str_replace(sprintf('{%s}', $key), $value, $path);
				}
			}
		}
		return $path;
	}

	// -----------------

	/**
	 * Returns a complete path to a given file in the framework folder, where `bootloader.php` is located.
	 */
	public static function toSourceFile(string $path): string
	{
		return self::toProjectFile($path, self::getFrameworkRootPath());
	}

	/**
	 * Returns a complete path to a given file in the project folder, where `app.php` is located.
	 */
	public static function toProjectFile(string $path, string|null $base = null): string
	{
		return ($base ?? self::getProjectRootPath()).'/'.ltrim($path, '/');
	}

	// -----------------
	
	protected static function getFrameworkRootPath(): string
	{
		return str_replace('\Support', '', dirname(__FILE__));
	}

	protected static function getProjectRootPath(): string
	{
		return defined('BASE_PATH') ? BASE_PATH : self::getFrameworkRootPath();
	}

}