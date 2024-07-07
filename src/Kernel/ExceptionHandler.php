<?php

/**
 * @copyright   Copyright (c), Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use JetBrains\PhpStorm\NoReturn;
use Throwable;

final class ExceptionHandler
{

	private static bool $debug_enabled = false;

	private static bool $log_enabled = false;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
//		error_reporting(getenv('ENABLE_DEBUG') === 'true' ? E_ALL : 0);

		self::$debug_enabled = getenv('ENABLE_DEBUG') === 'true';
		self::$log_enabled = getenv('ENABLE_LOGGING') === 'true';

		self::setPhpIniConfiguration();
	}

	// -----------------

	public static function handleThrowable(Throwable $throwable, bool $fatal = true): void
	{
		self::logThrowable($throwable, $fatal);

		if (self::$debug_enabled) {
			self::renderThrowableDebugView($throwable, $fatal);
		} else {
			ob_end_clean();
			http_response_code(500);
		}
	}

	public static function handleError(int $number, string $message, string $file, int $line): void
	{
		self::logError($number, $message, $file, $line);

		if (self::$debug_enabled) {
			self::renderErrorDebugView($number, $message, $file, $line);
		} else if ($number === E_ERROR) {
			// Only respond with HTTP 500 if the error is fatal, causing end of execution.
			ob_end_clean();
			http_response_code(500);
		}
	}

	// -----------------

	public static function logThrowable(Throwable $throwable, bool $fatal = true): void
	{
		// TODO: Implement logging of a throwable.
	}

	public static function logError(int $number, string $message, string $file, int $line): void
	{
		// TODO: Implement logging of an error.
	}

	// -----------------

	// rendering
	public static function renderThrowableDebugView(Throwable $throwable, bool $fatal = true): void
	{
		ob_clean();
		$request = self::getRequestInfo();
		$snippet = self::getSnippet($throwable->getFile());
		$traces = self::getFilteredTrace($throwable);

		include source('web/templates/debug_throwable.php');
		exit;
	}
	
	#[NoReturn]
	public static function renderErrorDebugView(int $number, string $message, string $file, int $line): void
	{
		ob_clean();
		$request = self::getRequestInfo();
		$snippet = self::getSnippet($file);

		include source('web/templates/debug_error.php');
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
			'full_url' => trim($scheme.'://'.$server.':'.$port.$url, '/')
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
					'->' => '<badge class="non-static">Non-Static</badge>',
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