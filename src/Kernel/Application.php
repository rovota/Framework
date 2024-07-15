<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use Rovota\Framework\Conversion\MarkupConverter;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Kernel\Enums\Environment;
use Rovota\Framework\Kernel\Exceptions\SystemRequirementException;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Version;

final class Application
{

	public const int DEFAULT_FLOAT_PRECISION = 14;

	// -----------------

	protected const string APP_VERSION = '1.0.0';

	protected const string PHP_MINIMUM_VERSION = '8.3.0';

	protected const array REQUIRED_EXTENSIONS = [
		'curl', 'exif', 'fileinfo', 'intl', 'mbstring', 'openssl', 'pdo', 'sodium', 'zip'
	];

	// -----------------

	public static Environment $environment;

	public static Version $version;
	public static Server $server;

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
		self::$server = new Server();

		self::serverCompatCheck();
		self::environmentCheck();

		// Foundation

		// Additional
		MarkupConverter::initialize();

		// Finish
		// TODO: Execute routes
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

		foreach (self::REQUIRED_EXTENSIONS as $extension) {
			if (!extension_loaded($extension)) {
				throw new SystemRequirementException(sprintf("The '%s' extension has to be installed and enabled.", $extension));
			}
		}
	}

	protected static function environmentCheck(): void
	{
		if (is_string(getenv('ENVIRONMENT'))) {
			self::$environment = Environment::tryFrom(getenv('ENVIRONMENT')) ?? Environment::Production;
			return;
		}

		$server_name = self::$server->get('server_name');
		$server_address = self::$server->get('server_addr');

		// Check for development
		if (Str::startsWithAny($server_name, ['dev.', 'local.', 'sandbox.']) || Str::endsWithAny($server_name, ['.localhost', '.local'])) {
			self::$environment = Environment::Development;
			return;
		}
		if ($server_address === '127.0.0.1' || $server_address === '::1' || $server_name === 'localhost') {
			self::$environment = Environment::Development;
			return;
		}

		// Check for testing
		if (Str::startsWithAny($server_name, ['test.', 'qa.', 'uat.', 'acceptance.', 'integration.']) || Str::endsWithAny($server_name, ['.test', '.example'])) {
			self::$environment = Environment::Testing;
			return;
		}

		// Check for staging
		if (Str::startsWithAny($server_name, ['stage.', 'staging.', 'prepod.'])) {
			self::$environment = Environment::Staging;
			return;
		}

		self::$environment = Environment::Production;
	}

}