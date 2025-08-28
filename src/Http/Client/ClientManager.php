<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client;

use Rovota\Framework\Http\Client\Drivers\Dynamic;
use Rovota\Framework\Http\Client\Enums\Driver;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\MisconfiguredServiceException;
use Rovota\Framework\Kernel\Exceptions\MissingInstanceException;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Path;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class ClientManager extends ServiceProvider
{

	/**
	 * @var Map<string, Client>
	 */
	protected Map $clients;

	protected string $default;

	// -----------------

	public function __construct()
	{
		$this->clients = new Map();

		$file = require Path::toProjectFile('config/client.php');

		foreach ($file['clients'] as $name => $config) {
			$this->clients->set($name, $this->build($name, $config));
		}

		$this->setDefault($file['default']);
	}

	// -----------------

	public function createClient(array $config, string|null $name = null): Client
	{
		return $this->build($name ?? Str::random(20), $config);
	}

	// -----------------

	public function has(string $name): bool
	{
		return isset($this->clients[$name]);
	}

	public function add(string $name, array $config): void
	{
		$this->clients[$name] = $this->build($name, $config);
	}

	public function get(string|null $name = null): Client
	{
		if ($name === null) {
			$name = $this->default;
		}

		if (isset($this->clients[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingInstanceException("The specified client could not be found: '$name'."));
		}

		return $this->clients[$name];
	}

	public function getWithDriver(Driver $driver): Client|null
	{
		return $this->clients->first(function (Client $store) use ($driver) {
			return $store->config->driver === $driver;
		});
	}

	// -----------------

	/**
	 * @returns Map<string, Client>
	 */
	public function all(): Map
	{
		return $this->clients;
	}

	// -----------------

	public function setDefault(string $name): void
	{
		if (isset($this->clients[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingInstanceException("Undefined clients cannot be set as default: '$name'."));
		}
		$this->default = $name;
	}

	public function getDefault(): string
	{
		return $this->default;
	}

	// -----------------

	protected function build(string $name, array $config): Client
	{
		$config = new ClientConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException($config->get('driver')));
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new MisconfiguredServiceException("The client '$name' cannot be used due to a configuration issue."));
		}

		return match ($config->driver) {
			Driver::Dynamic => new Dynamic($name, $config)
		};
	}

}