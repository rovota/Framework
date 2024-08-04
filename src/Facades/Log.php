<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Rovota\Framework\Logging\Interfaces\ChannelInterface;
use Rovota\Framework\Logging\LoggingManager;
use Stringable;

final class Log
{

	protected function __construct()
	{
	}

	// -----------------

	public static function channel(string $name): ChannelInterface|null
	{
		return LoggingManager::getChannel($name);
	}

	// -----------------

	public static function stack(array $channels, string|null $name = null): ChannelInterface|null
	{
		return LoggingManager::createStack($channels, $name);
	}

	public static function create(array $config, string|null $name = null): ChannelInterface|null
	{
		return LoggingManager::createChannel($config, $name);
	}

	// -----------------

	public static function debug(string|Stringable $message, array $context = []): void
	{
		LoggingManager::getChannel()?->debug($message, $context);
	}

	public static function info(string|Stringable $message, array $context = []): void
	{
		LoggingManager::getChannel()?->info($message, $context);
	}

	public static function notice(string|Stringable $message, array $context = []): void
	{
		LoggingManager::getChannel()?->notice($message, $context);
	}

	public static function warning(string|Stringable $message, array $context = []): void
	{
		LoggingManager::getChannel()?->warning($message, $context);
	}

	public static function error(string|Stringable $message, array $context = []): void
	{
		LoggingManager::getChannel()?->error($message, $context);
	}

	public static function critical(string|Stringable $message, array $context = []): void
	{
		LoggingManager::getChannel()?->critical($message, $context);
	}

	public static function alert(string|Stringable $message, array $context = []): void
	{
		LoggingManager::getChannel()?->alert($message, $context);
	}

	public static function emergency(string|Stringable $message, array $context = []): void
	{
		LoggingManager::getChannel()?->emergency($message, $context);
	}

}