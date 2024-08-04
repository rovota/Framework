<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Rovota\Framework\Logging\Channel;
use Rovota\Framework\Logging\Drivers\Stack;
use Rovota\Framework\Logging\Interfaces\ChannelInterface;
use Rovota\Framework\Logging\Logging;
use Stringable;

final class Log
{

	protected function __construct()
	{
	}

	// -----------------

	public static function channel(string $name): ChannelInterface|null
	{
		return Logging::get($name);
	}

	// -----------------

	public static function stack(array $channels, string|null $name = null): ChannelInterface
	{
		return Stack::create($channels, $name);
	}

	public static function build(array $options, string|null $name = null): ChannelInterface
	{
		return Channel::create($options, $name);
	}

	// -----------------

	public static function debug(string|Stringable $message, array $context = []): void
	{
		Logging::get()?->debug($message, $context);
	}

	public static function info(string|Stringable $message, array $context = []): void
	{
		Logging::get()?->info($message, $context);
	}

	public static function notice(string|Stringable $message, array $context = []): void
	{
		Logging::get()?->notice($message, $context);
	}

	public static function warning(string|Stringable $message, array $context = []): void
	{
		Logging::get()?->warning($message, $context);
	}

	public static function error(string|Stringable $message, array $context = []): void
	{
		Logging::get()?->error($message, $context);
	}

	public static function critical(string|Stringable $message, array $context = []): void
	{
		Logging::get()?->critical($message, $context);
	}

	public static function alert(string|Stringable $message, array $context = []): void
	{
		Logging::get()?->alert($message, $context);
	}

	public static function emergency(string|Stringable $message, array $context = []): void
	{
		Logging::get()?->emergency($message, $context);
	}

}