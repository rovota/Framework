<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage;

use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\MisconfiguredServiceException;
use Rovota\Framework\Kernel\Exceptions\MissingInstanceException;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Storage\Drivers\AsyncS3;
use Rovota\Framework\Storage\Drivers\Custom;
use Rovota\Framework\Storage\Drivers\Local;
use Rovota\Framework\Storage\Drivers\S3;
use Rovota\Framework\Storage\Drivers\Sftp;
use Rovota\Framework\Storage\Enums\Driver;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class StorageManager extends ServiceProvider
{

	/**
	 * @var Map<string, Disk>
	 */
	protected Map $disks;

	protected array $configs = [];

	public readonly string $default;

	// -----------------

	public function __construct()
	{
		$this->disks = new Map();

		$file = StorageConfig::load('config/storage');

		foreach ($file->disks as $name => $config) {
			$this->define($name, $config);
		}

		if (count($file->disks) > 0 && isset($this->disks[$file->default])) {
			$this->default = $this->disks[$file->default];
		}
	}

	// -----------------

	public function createDisk(array $config, string|null $name = null): Disk
	{
		return $this->build($name ?? Str::random(20), $config);
	}

	// -----------------

	public function define(string $name, array $config): void
	{
		$this->configs[$name] = $config;

		if ($config['auto_connect'] === true) {
			$this->connect($name);
		}
	}

	public function connect(string $name): void
	{
		if (isset($this->configs[$name])) {
			$this->disks->set($name, $this->build($name, $this->configs[$name]));
		}
	}

	// -----------------

	public function has(string $name): bool
	{
		return isset($this->disks[$name]);
	}

	public function add(string $name, array $config): void
	{
		$this->disks[$name] = $this->build($name, $config);
	}

	public function get(string|null $name = null): Disk
	{
		if ($name === null && property_exists($this, 'default')) {
			$name = $this->default;
		}

		if (isset($this->disks[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingInstanceException("The specified disk could not be found: '$name'."));
		}

		return $this->disks[$name];
	}

	public function getWithDriver(Driver $driver): Disk|null
	{
		return $this->disks->first(function (Disk $disk) use ($driver) {
			return $disk->config->driver === $driver;
		});
	}

	// -----------------

	/**
	 * @returns Map<string, Disk>
	 */
	public function all(): Map
	{
		return $this->disks;
	}

	// -----------------

	public function isDefined(string $name): bool
	{
		return array_key_exists($name, $this->configs);
	}

	public function isConnected(string $name): bool
	{
		return $this->disks->hasKey($name);
	}

	// -----------------

	protected function build(string $name, array $config): Disk
	{
		$config = new DiskConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException($config->get('driver')));
			exit;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new MisconfiguredServiceException("The disk '$name' cannot be used due to a configuration issue."));
			exit;
		}

		return match ($config->driver) {
			Driver::Custom => new Custom($name, $config),
			Driver::Local => new Local($name, $config),
			Driver::AsyncS3 => new AsyncS3($name, $config),
			Driver::S3 => new S3($name, $config),
			Driver::Sftp => new Sftp($name, $config),
		};
	}

}