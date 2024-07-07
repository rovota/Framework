<?php

/**
 * @copyright   Copyright (c), Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Kernel\Enums\Environment;
use Rovota\Framework\Kernel\Exceptions\SystemRequirementException;
use Rovota\Framework\Support\Version;

final class Application
{

	protected const string APP_VERSION = '1.0.0';

	protected const string PHP_MINIMUM_VERSION = '8.3.0';

	protected const array REQUIRED_EXTENSIONS = [
		'curl', 'exif', 'fileinfo', 'intl', 'mbstring', 'openssl', 'pdo', 'sodium', 'zip'
	];

	// -----------------

	public static Environment $environment;

	public static Version $version;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @throws SystemRequirementException
	 */
	public static function start(): void
	{
		self::$version = new Version(self::APP_VERSION);

		self::serverCompatCheck();
		self::environmentCheck();

		echo PHP_VERSION;
	}

	public static function shutdown(): void
	{
		$error = error_get_last();
		if ($error !== null && $error['type'] === E_ERROR) {
			array_shift($error);
			ExceptionHandler::handleError(E_ERROR, ...$error);
		}
	}

	// -----------------

	public static function quit(StatusCode $code): never
	{
		http_response_code($code->value);
		exit;
	}

	// -----------------

	public static function getRawVersion(): string
	{
		return self::APP_VERSION;
	}

	// -----------------

	public static function getEnvironment(): Environment
	{
		return self::$environment;
	}

	public static function hasEnvironment(array|string $name): bool
	{
		foreach (is_array($name) ? $name : [$name] as $name) {
			if (Environment::tryFrom($name) === self::$environment) {
				return true;
			}
		}

		return false;
	}

	public static function hasDebugEnabled(): bool
	{
		return getenv('ENABLE_DEBUG') === 'true';
	}

	public static function hasLoggingEnabled(): bool
	{
		return getenv('ENABLE_LOGGING') === 'true';
	}

	// -----------------

	/**
	 * @throws SystemRequirementException
	 */
	protected static function serverCompatCheck(): void
	{
		if (version_compare(PHP_VERSION, self::PHP_MINIMUM_VERSION, '<')) {
			throw new SystemRequirementException(sprintf('PHP %s or newer has to be installed.', self::PHP_MINIMUM_VERSION));
		}

		foreach (self::REQUIRED_EXTENSIONS as $required_extension) {
			if (!extension_loaded($required_extension)) {
				throw new SystemRequirementException(sprintf("The '%s' extension has to be installed and enabled.", $required_extension));
			}
		}
	}

	protected static function environmentCheck(): void
	{
		if (is_string(getenv('ENVIRONMENT'))) {
			self::$environment = Environment::tryFrom(getenv('ENVIRONMENT')) ?? Environment::Production;
			return;
		}

		self::$environment = Environment::Production;
	}

}