<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth;

use Rovota\Framework\Auth\Drivers\Standard;
use Rovota\Framework\Auth\Enums\Driver;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\MisconfiguredServiceException;
use Rovota\Framework\Kernel\Exceptions\MissingInstanceException;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Security\CsrfManager;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class AuthManager extends ServiceProvider
{

	/**
	 * @var Map<string, Provider>
	 */
	protected Map $providers;

	public readonly string $default;

	// -----------------

	public function __construct()
	{
		$this->providers = new Map();

		CsrfManager::initialize();

		$file = AuthConfig::load('config/auth');

		foreach ($file->providers as $name => $config) {
			$this->providers->set($name, $this->build($name, $config));
		}

		if (count($file->providers) > 0 && isset($this->providers[$file->default])) {
			$this->default = $this->providers[$file->default];
		}
	}

	// -----------------

	public function createProvider(array $config, string|null $name = null): Provider
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
		$this->providers[$name] = $this->build($name, $config);
	}

	public function get(string|null $name = null): Provider
	{
		if ($name === null && property_exists($this, 'default')) {
			$name = $this->default;
		}

		if (isset($this->providers[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingInstanceException("The specified provider could not be found: '$name'."));
		}

		return $this->providers[$name];
	}

	// -----------------

	/**
	 * @returns Map<string, Provider>
	 */
	public function all(): Map
	{
		return $this->providers;
	}

	// -----------------

	protected function build(string $name, array $config): Provider
	{
		$config = new ProviderConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException($config->get('driver')));
			exit;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new MisconfiguredServiceException("The provider '$name' cannot be used due to a configuration issue."));
			exit;
		}

		return match ($config->driver) {
			Driver::Standard => new Standard($name, $config),
		};
	}

}