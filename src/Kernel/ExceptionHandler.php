<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use Monolog\Level;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Logging\LoggingManager;
use Rovota\Framework\Support\Buffer;
use Rovota\Framework\Support\Enums\PHPErrorLevel;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Path;
use Rovota\Framework\Support\Text;
use Throwable;

final class ExceptionHandler
{

	private static bool $debug_enabled = false;

	private static bool $log_enabled = false;

	// -----------------

	private static LoggingManager $logging_manager;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 */
	public static function initialize(): void
	{
		self::$debug_enabled = getenv('ENABLE_DEBUG') === 'true';
		self::$log_enabled = getenv('ENABLE_LOGGING') === 'true';

		try {
			self::$logging_manager = new LoggingManager();
		} catch (Throwable $exception) {
			echo $exception->getMessage();
		}

		self::setPhpIniConfiguration();
	}

	// -----------------

	public static function handleThrowable(Throwable $throwable): void
	{
		self::logThrowable($throwable);

		if (self::$debug_enabled) {
			self::renderThrowableDebugView($throwable);
		} else {
			Buffer::end();
			Framework::quit(StatusCode::InternalServerError);
		}
	}

	public static function handleError(int $number, string $message, string $file, int $line): void
	{
		self::logError($number, $message, $file, $line);

		if (self::$debug_enabled) {
			self::renderErrorDebugView($number, $message, $file, $line);
		} else if ($number === E_ERROR) {
			Buffer::end();
			Framework::quit(StatusCode::InternalServerError);
		}
	}

	// -----------------

	public static function logThrowable(Throwable $throwable): void
	{
		if (self::$log_enabled) {
			self::$logging_manager->get()->log(Level::Critical, $throwable->getMessage(), [
				$throwable::class, $throwable->getFile(), $throwable->getLine(), self::getRequestInfo()['full_url']
			]);
		}
	}

	public static function logError(int $number, string $message, string $file, int $line): void
	{
		if (self::$log_enabled) {
			self::$logging_manager->get()->error($message, [
				PHPErrorLevel::tryFrom($number)?->label() ?? 'Unknown Error', $file, $line
			]);
		}
	}

	// -----------------

	public static function renderThrowableDebugView(Throwable $throwable): never
	{
		Buffer::erase();
		$request = self::getRequestInfo();
		$solution = $throwable instanceof ProvidesSolution ? $throwable->solution() : null;
		$snippet = self::getSnippet($throwable->getFile());
		$traces = self::getFilteredTrace($throwable);

		include Path::toSourceFile('web/templates/debug_throwable.php');
		exit;
	}

	public static function renderErrorDebugView(int $number, string $message, string $file, int $line): never
	{
		Buffer::erase();
		$request = self::getRequestInfo();
		$snippet = self::getSnippet($file);

		include Path::toSourceFile('web/templates/debug_error.php');
		exit;
	}

	// -----------------

	protected static function getRequestInfo(): array
	{
		$scheme = $_SERVER['REQUEST_SCHEME'] ?? 'http';
		$server = $_SERVER['SERVER_NAME'] ?? 'localhost';
		$port = $_SERVER['SERVER_PORT'] ?? '80';
		$url = $_SERVER['REQUEST_URI'] ?? '/';

		return [
			'scheme' => $scheme,
			'server' => $server,
			'port' => $port,
			'url' => $url,
			'full_url' => trim($scheme . '://' . $server . ':' . $port . $url, '/')
		];
	}

	protected static function getSnippet(string $file): array
	{
		try {
			$content = file($file, FILE_IGNORE_NEW_LINES);
			if ($content === false) {
				return [];
			}
		} catch (Throwable) {
			return [];
		}

		return $content;
	}

	protected static function getFilteredTrace(Throwable $throwable): array
	{
		$filtered = [];

		$filtered[0] = ['line' => $throwable->getLine(), 'function' => '', 'file' => trim(str_replace(dirname(str_replace('/', '\\', $_SERVER['SCRIPT_FILENAME']), 2), '', $throwable->getFile()), '\\'), 'class' => null, 'type' => '',];

		foreach ($throwable->getTrace() as $key => $trace) {
			$filtered[$key + 1] = [
				'line' => $trace['line'] ?? '#',
				'function' => $trace['function'],
				'file' => trim(str_replace(dirname(str_replace('/', '\\', $_SERVER['SCRIPT_FILENAME']), 2), '', $trace['file'] ?? ''), '\\'),
				'class' => $trace['class'] ?? null,
				'type' => match ($trace['type'] ?? null) {
					'::' => '<badge class="static">Static</badge>',
					'->' => '<badge class="dynamic">Non-Static</badge>',
					default => '',
				},];
		}

		return $filtered;
	}

	// -----------------

	protected static function setPhpIniConfiguration(): void
	{
		ini_set('display_errors', 1);
		ini_set('log_errors', self::$log_enabled ? 1 : 0);
	}

}