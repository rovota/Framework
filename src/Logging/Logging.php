<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging;

use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Logging\Drivers\Discord;
use Rovota\Framework\Logging\Drivers\Monolog;
use Rovota\Framework\Logging\Drivers\Stack;
use Rovota\Framework\Logging\Drivers\Stream;
use Rovota\Framework\Logging\Enums\Driver;
use Rovota\Framework\Logging\Exceptions\ChannelMisconfigurationException;
use Rovota\Framework\Logging\Exceptions\MissingChannelConfigException;
use Rovota\Framework\Logging\Interfaces\ChannelInterface;
use Rovota\Framework\Support\Internal;

final class Logging
{

	/**
	 * @var array<string, ChannelInterface>
	 */
	protected static array $channels = [];

	protected static string $default;

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
		$config = require Internal::projectFile('config/logging.php');

		foreach ($config['channels'] as $name => $options) {
			$channel =  self::build($name, $options);
			if ($channel instanceof ChannelInterface) {
				self::$channels[$name] = $channel;
			}
		}

		self::setDefault($config['default']);
	}

	// -----------------

	public static function build(string $name, array $config): ChannelInterface|null
	{
		$config = new ChannelConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException("The selected driver '{$config->get('driver')}' is not supported."));
			return null;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new ChannelMisconfigurationException("The channel '$name' cannot be used due to a configuration issue."));
			return null;
		}

		return match($config->driver) {
			Driver::Discord => new Discord($name, $config),
			Driver::Monolog => new Monolog($name, $config),
			Driver::Stack => new Stack($name, $config),
			Driver::Stream => new Stream($name, $config),
			default => null,
		};
	}

	// -----------------

	public static function add(string $name, array $config): void
	{
		$channel = self::build($name, $config);

		if ($channel instanceof ChannelInterface) {
			self::$channels[$name] = $channel;
		}
	}

	public static function get(string|null $name = null): ChannelInterface|null
	{
		if ($name === null) {
			$name = self::$default;
		}

		return self::$channels[$name] ?? null;
	}

	// -----------------

	/**
	 * @returns array<string, ChannelInterface>
	 */
	public static function all(): array
	{
		return self::$channels;
	}

	// -----------------

	public static function setDefault(string $name): void
	{
		if (isset(self::$channels[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingChannelConfigException("Undefined channels cannot be set as default: '$name'."));
		}
		self::$default = $name;
	}

	public static function getDefault(): string
	{
		return self::$default;
	}

}