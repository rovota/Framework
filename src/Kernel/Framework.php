<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use Rovota\Framework\Conversion\MarkupConverter;
use Rovota\Framework\Conversion\TextConverter;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Throttling\LimitManager;
use Rovota\Framework\Kernel\Exceptions\SystemRequirementException;
use Rovota\Framework\Routing\RouteManager;

final class Framework
{

	protected const string APP_VERSION = '1.0.0';

	protected const string PHP_MINIMUM_VERSION = '8.4.0';

	// -----------------

	protected static Version $version;
	protected static Environment $environment;

	protected static ServiceContainer $services;

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

		TextConverter::initialize();
		MarkupConverter::initialize();

		self::reliabilityCheck();
		self::serverCompatCheck();
		self::createEnvironment();
		self::configureServices();
		self::configureLimiters();

		// Finish
		RouteManager::instance()->importRoutes();
		RouteManager::instance()->router->run();
	}

	public static function shutdown(): void
	{
		if (session_status() === PHP_SESSION_ACTIVE) {
			session_gc();
		}

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

	public static function environment(): Environment
	{
		return self::$environment;
	}

	public static function services(): ServiceContainer
	{
		return self::$services;
	}

	public static function service(string $name): object|null
	{
		if (str_contains($name, '\\')) {
			return self::$services->resolve($name);
		}

		return self::$services->get($name);
	}

	// -----------------

	protected static function createEnvironment(): void
	{
		self::$environment = new Environment();
	}

	// -----------------

	protected static function configureServices(): void
	{
		self::$services = new ServiceContainer();

		foreach (self::$environment->config->services as $name => $class) {
			self::$services->register($class, $name);
		}
	}

	protected static function configureLimiters(): void
	{
		foreach (self::$environment->config->limiters as $name => $closure) {
			LimitManager::instance()->define($name, $closure);
		}
	}

	// -----------------

	protected static function reliabilityCheck(): void
	{
		if (isset($_SERVER['HTTP_HOST']) === false || $_SERVER['HTTP_HOST'] === '' || $_SERVER['HTTP_HOST'] === null) {
			http_response_code(400);
			exit;
		}
	}

	/**
	 * @throws SystemRequirementException
	 */
	protected static function serverCompatCheck(): void
	{
		if (version_compare(PHP_VERSION, self::PHP_MINIMUM_VERSION, '<')) {
			throw new SystemRequirementException(sprintf('PHP %s or newer has to be installed.', self::PHP_MINIMUM_VERSION));
		}

		foreach (['curl', 'exif', 'fileinfo', 'intl', 'mbstring', 'openssl', 'pdo', 'sodium', 'zip'] as $extension) {
			if (!extension_loaded($extension)) {
				throw new SystemRequirementException(sprintf("The '%s' extension has to be installed and enabled.", $extension));
			}
		}
	}

}