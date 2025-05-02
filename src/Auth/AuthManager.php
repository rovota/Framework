<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth;

use Rovota\Framework\Auth\Drivers\Standard;
use Rovota\Framework\Auth\Enums\Driver;
use Rovota\Framework\Auth\Exceptions\MissingProviderException;
use Rovota\Framework\Auth\Exceptions\ProviderMisconfigurationException;
use Rovota\Framework\Auth\Interfaces\ProviderInterface;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Security\CsrfManager;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Path;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class AuthManager extends ServiceProvider
{

	/**
	 * @var Map<string, ProviderInterface>
	 */
	protected Map $providers;

	protected string $default;

	// -----------------

	public function __construct()
	{
		$this->providers = new Map();

		CsrfManager::initialize();

		$file = require Path::toProjectFile('config/auth.php');

		foreach ($file['providers'] as $name => $config) {
			$provider = $this->build($name, $config);
			if ($provider instanceof ProviderInterface) {
				$this->providers->set($name, $provider);
			}
		}

		$this->setDefault($file['default']);
	}

	// -----------------

	public function createProvider(array $config, string|null $name = null): ProviderInterface|null
	{
		return $this->build($name ?? Str::random(20), $config);
	}

	// -----------------

	public function has(string $name): bool
	{
		return isset($this->providers[$name]);
	}

	public function add(string $name, array $config): void
	{
		$provider = $this->build($name, $config);

		if ($provider instanceof ProviderInterface) {
			$this->providers[$name] = $provider;
		}
	}

	public function get(string|null $name = null): ProviderInterface
	{
		if ($name === null) {
			$name = $this->default;
		}

		if (isset($this->providers[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingProviderException("The specified provider could not be found: '$name'."));
		}

		return $this->providers[$name];
	}

	public function getWithDriver(Driver $driver): ProviderInterface|null
	{
		return $this->providers->first(function (ProviderInterface $provider) use ($driver) {
			return $provider->config->driver === $driver;
		});
	}

	// -----------------

	/**
	 * @returns Map<string, ProviderInterface>
	 */
	public function all(): Map
	{
		return $this->providers;
	}

	// -----------------

	public function setDefault(string $name): void
	{
		if (isset($this->providers[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingProviderException("Undefined providers cannot be set as default: '$name'."));
		}
		$this->default = $name;
	}

	public function getDefault(): string
	{
		return $this->default;
	}

	// -----------------

	protected function build(string $name, array $config): ProviderInterface|null
	{
		$config = new ProviderConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException($config->get('driver')));
			return null;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new ProviderMisconfigurationException("The provider '$name' cannot be used due to a configuration issue."));
			return null;
		}

		return match($config->driver) {
			Driver::Standard => new Standard($name, $config),
			default => null,
		};
	}

}