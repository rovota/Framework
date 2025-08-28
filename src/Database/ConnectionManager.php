<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database;

use Rovota\Framework\Database\Drivers\MySql;
use Rovota\Framework\Database\Enums\Driver;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\MisconfiguredServiceException;
use Rovota\Framework\Kernel\Exceptions\MissingInstanceException;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class ConnectionManager extends ServiceProvider
{

	/**
	 * @var Map<string, Connection>
	 */
	protected Map $connections;

	public readonly string $default;

	// -----------------

	public function __construct()
	{
		$this->connections = new Map();

		$file = DatabaseConfig::load('config/databases');

		foreach ($file->connections as $name => $config) {
			$this->connections->set($name, $this->build($name, $config));
		}

		if (count($file->connections) > 0 && isset($this->connections[$file->default])) {
			$this->default = $this->connections[$file->default];
		}
	}

	// -----------------

	public function createConnection(array $config, string|null $name = null): Connection
	{
		return self::build($name ?? Str::random(20), $config);
	}

	// -----------------

	public function has(string $name): bool
	{
		return isset($this->connections[$name]);
	}

	public function add(string $name, array $config): void
	{
		$this->connections[$name] = self::build($name, $config);
	}

	public function get(string|null $name = null): Connection
	{
		if ($name === null && property_exists($this, 'default')) {
			$name = $this->default;
		}

		if (isset($this->connections[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingInstanceException("The specified connection could not be found: '$name'."));
		}

		return $this->connections[$name];
	}

	public function getWithDriver(Driver $driver): Connection|null
	{
		return $this->connections->first(function (Connection $connection) use ($driver) {
			return $connection->config->driver === $driver;
		});
	}

	// -----------------

	/**
	 * @returns Map<string, Connection>
	 */
	public function all(): Map
	{
		return $this->connections;
	}

	// -----------------

	protected function build(string $name, array $config): Connection
	{
		$config = new ConnectionConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException($config->get('driver')));
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new MisconfiguredServiceException("The connection '$name' cannot be used due to a configuration issue."));
		}

		return match ($config->driver) {
			Driver::MySql => new MySql($name, $config),
		};
	}

}