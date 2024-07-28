<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use Rovota\Framework\Conversion\MarkupConverter;
use Rovota\Framework\Conversion\TextConverter;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Request;
use Rovota\Framework\Kernel\Enums\EnvironmentType;
use Rovota\Framework\Kernel\Exceptions\SystemRequirementException;
use Rovota\Framework\Localization\Localization;
use Rovota\Framework\Security\Encryption;
use Rovota\Framework\Security\Exceptions\IncorrectKeyException;

final class Application
{

	public const int DEFAULT_FLOAT_PRECISION = 14;

	public const int DEFAULT_BCRYPT_COST = 12;

	// -----------------

	protected const string APP_VERSION = '1.0.0';

	protected const string PHP_MINIMUM_VERSION = '8.3.0';

	// -----------------

	protected const array REQUIRED_EXTENSIONS = [
		'curl', 'exif', 'fileinfo', 'intl', 'mbstring', 'openssl', 'pdo', 'sodium', 'zip'
	];

	// -----------------

	protected static Version $version;
	protected static DefaultEnvironment $environment;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @throws SystemRequirementException
	 * @throws IncorrectKeyException
	 */
	public static function start(): void
	{
		self::$version = new Version(self::APP_VERSION);

		self::createEnvironment();
		self::serverCompatCheck();

		// Foundation
		Encryption::initialize();
		Request::initialize();
		Localization::initialize();

		// Additional
		TextConverter::initialize();
		MarkupConverter::initialize();

		// Finish
		// TODO: Execute routes

		// TODO: From services like Auth, call App\Environment class methods to load configs.
		// For example, App\Environment::authProviders() returns an array with auth provider classes/config.
		// And App\Environment::libraries() returns an array of library classes to call a load() method on.
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

	public static function version(): Version
	{
		return self::$version;
	}

	public static function rawVersion(): string
	{
		return self::APP_VERSION;
	}

	// -----------------

	public static function environment(): DefaultEnvironment
	{
		return self::$environment;
	}

	public static function hasEnvironmentType(array|string $name): bool
	{
		foreach (is_array($name) ? $name : [$name] as $name) {
			if (EnvironmentType::tryFrom($name) === self::$environment->type) {
				return true;
			}
		}

		return false;
	}

	// -----------------

	public static function createEnvironment(): void
	{
		if (class_exists('\App\Setup\Environment')) {
			self::$environment = new \App\Setup\Environment();
		}

		self::$environment = new DefaultEnvironment();
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

		foreach (self::REQUIRED_EXTENSIONS as $extension) {
			if (!extension_loaded($extension)) {
				throw new SystemRequirementException(sprintf("The '%s' extension has to be installed and enabled.", $extension));
			}
		}
	}

}