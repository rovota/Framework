<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging;

use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Logging\Drivers\Discord;
use Rovota\Framework\Logging\Drivers\Monolog;
use Rovota\Framework\Logging\Drivers\Stack;
use Rovota\Framework\Logging\Drivers\Stream;
use Rovota\Framework\Logging\Enums\Driver;
use Rovota\Framework\Logging\Exceptions\ChannelMisconfigurationException;
use Rovota\Framework\Logging\Exceptions\MissingChannelException;
use Rovota\Framework\Logging\Interfaces\ChannelInterface;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Path;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class LoggingManager extends ServiceProvider
{

	/**
	 * @var Map<string, ChannelInterface>
	 */
	protected Map $channels;

	protected string $default;

	// -----------------

	public function __construct()
	{
		$this->channels = new Map();

		$file = require Path::toProjectFile('config/logging.php');

		foreach ($file['channels'] as $name => $config) {
			$channel = $this->build($name, $config);
			if ($channel instanceof ChannelInterface) {
				$this->channels->set($name, $channel);
			}
		}

		$this->setDefault($file['default']);
	}

	// -----------------

	public function createChannel(array $config, string|null $name = null): ChannelInterface|null
	{
		return self::build($name ?? Str::random(20), $config);
	}

	public function createStack(array $channels, string|null $name = null): ChannelInterface|null
	{
		return self::build($name ?? Str::random(20), [
			'driver' => 'stack',
			'label' => 'Unnamed Channel',
			'channels' => $channels,
		]);
	}

	// -----------------

	public function has(string $name): bool
	{
		return isset($this->channels[$name]);
	}

	public function add(string $name, array $config): void
	{
		$channel = self::build($name, $config);

		if ($channel instanceof ChannelInterface) {
			$this->channels[$name] = $channel;
		}
	}

	public function get(string|null $name = null): ChannelInterface
	{
		if ($name === null) {
			$name = $this->default;
		}

		if (isset($this->channels[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingChannelException("The specified channel could not be found: '$name'."));
		}

		return $this->channels[$name];
	}

	// -----------------

	/**
	 * @returns Map<string, ChannelInterface>
	 */
	public function all(): Map
	{
		return $this->channels;
	}

	// -----------------

	public function setDefault(string $name): void
	{
		if (isset($this->channels[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingChannelException("Undefined channels cannot be set as default: '$name'."));
		}
		$this->default = $name;
	}

	public function getDefault(): string
	{
		return $this->default;
	}

	// -----------------

	protected function build(string $name, array $config): ChannelInterface|null
	{
		$config = new ChannelConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException($config->get('driver')));
			return null;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new ChannelMisconfigurationException("The channel '$name' cannot be used due to a configuration issue."));
			return null;
		}

		return match ($config->driver) {
			Driver::Discord => new Discord($name, $config),
			Driver::Monolog => new Monolog($name, $config),
			Driver::Stack => new Stack($name, $config),
			Driver::Stream => new Stream($name, $config),
			default => null,
		};
	}

}