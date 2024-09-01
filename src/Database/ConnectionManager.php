<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database;

use Rovota\Framework\Database\Drivers\MySql;
use Rovota\Framework\Database\Enums\Driver;
use Rovota\Framework\Database\Exceptions\ConnectionMisconfigurationException;
use Rovota\Framework\Database\Exceptions\MissingConnectionException;
use Rovota\Framework\Database\Interfaces\ConnectionInterface;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Path;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class ConnectionManager extends ServiceProvider
{

	/**
	 * @var Map<string, ConnectionInterface>
	 */
	protected Map $connections;

	protected string $default;

	// -----------------

	public function __construct()
	{
		$this->connections = new Map();

		$file = require Path::toProjectFile('config/databases.php');

		foreach ($file['connections'] as $name => $config) {
			$connection = $this->build($name, $config);
			if ($connection instanceof ConnectionInterface) {
				$this->connections->set($name, $connection);
			}
		}

		$this->setDefault($file['default']);
	}

	// -----------------

	public function create(array $config, string|null $name = null): ConnectionInterface|null
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
		$channel = self::build($name, $config);

		if ($channel instanceof ConnectionInterface) {
			$this->connections[$name] = $channel;
		}
	}

	public function get(string|null $name = null): ConnectionInterface
	{
		if ($name === null) {
			$name = $this->default;
		}

		if (isset($this->connections[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingConnectionException("The specified connection could not be found: '$name'."));
		}

		return $this->connections[$name];
	}

	// -----------------

	/**
	 * @returns Map<string, ConnectionInterface>
	 */
	public function all(): Map
	{
		return $this->connections;
	}

	// -----------------

	public function setDefault(string $name): void
	{
		if (isset($this->connections[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingConnectionException("Undefined connections cannot be set as default: '$name'."));
		}
		$this->default = $name;
	}

	public function getDefault(): string
	{
		return $this->default;
	}

	// -----------------

	protected function build(string $name, array $config): ConnectionInterface|null
	{
		$config = new ConnectionConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException($config->get('driver')));
			return null;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new ConnectionMisconfigurationException("The connection '$name' cannot be used due to a configuration issue."));
			return null;
		}

		return match($config->driver) {
			Driver::MySql => new MySql($name, $config),
			default => null,
		};
	}

}