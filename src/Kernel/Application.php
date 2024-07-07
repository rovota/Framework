<?php

/**
 * @copyright   Copyright (c), Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use Rovota\Framework\Support\Version;

final class Application
{

	protected const string APP_VERSION = '1.0.0';

	protected const string PHP_MINIMUM_VERSION = '8.3.0';

	protected const array REQUIRED_EXTENSIONS = [
		'curl', 'exif', 'fileinfo', 'intl', 'mbstring', 'openssl', 'pdo', 'sodium', 'zip'
	];

	// -----------------

	public static string $environment = 'development';

	public static Version $version;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function start(): void
	{
		self::$version = new Version(self::APP_VERSION);

		echo PHP_VERSION;
	}

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

}