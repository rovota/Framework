<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging;

use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\MisconfiguredServiceException;
use Rovota\Framework\Kernel\Exceptions\MissingInstanceException;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Logging\Drivers\Discord;
use Rovota\Framework\Logging\Drivers\Monolog;
use Rovota\Framework\Logging\Drivers\Stack;
use Rovota\Framework\Logging\Drivers\Stream;
use Rovota\Framework\Logging\Enums\Driver;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class LoggingManager extends ServiceProvider
{

	/**
	 * @var Map<string, Channel>
	 */
	protected Map $channels;

	public readonly string $default;

	// -----------------

	public function __construct()
	{
		$this->channels = new Map();

		$file = LoggingConfig::load('config/logging');

		foreach ($file->channels as $name => $config) {
			$this->channels->set($name, $this->build($name, $config));
		}

		if (count($file->channels) > 0 && isset($this->channels[$file->default])) {
			$this->default = $this->channels[$file->default];
		}
	}

	// -----------------

	public function createChannel(array $config, string|null $name = null): Channel|null
	{
		return $this->build($name ?? Str::random(20), $config);
	}

	public function createStack(array $channels, string|null $name = null): Channel|null
	{
		return $this->build($name ?? Str::random(20), [
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
		$this->channels[$name] = $this->build($name, $config);
	}

	public function get(string|null $name = null): Channel
	{
		if ($name === null && property_exists($this, 'default')) {
			$name = $this->default;
		}

		if (isset($this->channels[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingInstanceException("The specified channel could not be found: '$name'."));
		}

		return $this->channels[$name];
	}

	// -----------------

	/**
	 * @returns Map<string, Channel>
	 */
	public function all(): Map
	{
		return $this->channels;
	}

	// -----------------

	protected function build(string $name, array $config): Channel
	{
		$config = new ChannelConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException($config->get('driver')));
			exit;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new MisconfiguredServiceException("The channel '$name' cannot be used due to a configuration issue."));
			exit;
		}

		return match ($config->driver) {
			Driver::Discord => new Discord($name, $config),
			Driver::Monolog => new Monolog($name, $config),
			Driver::Stack => new Stack($name, $config),
			Driver::Stream => new Stream($name, $config),
		};
	}

}