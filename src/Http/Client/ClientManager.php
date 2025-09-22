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

	public readonly string $default;

	// -----------------

	public function __construct()
	{
		$this->clients = new Map();

		$file = require Path::toProjectFile('config/client.php');

		foreach ($file['clients'] as $name => $config) {
			$this->clients->set($name, $this->build($name, $config));
		}

		if (count($file['clients']) > 0 && isset($this->clients[$file['default']])) {
			$this->default = $this->clients[$file['default']];
		}
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
		if ($name === null && property_exists($this, 'default')) {
			$name = $this->default;
		}

		if (isset($this->clients[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingInstanceException("The specified client could not be found: '$name'."));
		}

		return $this->clients[$name];
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