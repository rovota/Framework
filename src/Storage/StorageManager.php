<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage;

use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Storage\Drivers\AsyncS3;
use Rovota\Framework\Storage\Drivers\Custom;
use Rovota\Framework\Storage\Drivers\Local;
use Rovota\Framework\Storage\Drivers\S3;
use Rovota\Framework\Storage\Enums\Driver;
use Rovota\Framework\Storage\Exceptions\DiskMisconfigurationException;
use Rovota\Framework\Storage\Exceptions\MissingDiskException;
use Rovota\Framework\Storage\Interfaces\DiskInterface;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Path;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class StorageManager extends ServiceProvider
{

	/**
	 * @var Map<string, DiskInterface>
	 */
	protected Map $disks;

	protected array $configs = [];

	protected string $default;

	// -----------------

	public function __construct()
	{
		$this->disks = new Map();

		$file = require Path::toProjectFile('config/storage.php');

		foreach ($file['disks'] as $name => $config) {
			$this->define($name, $config);
		}

		$this->setDefault($file['default']);
	}

	// -----------------

	public function createDisk(array $config, string|null $name = null): DiskInterface|null
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
			$disk = $this->build($name, $this->configs[$name]);
			if ($disk instanceof DiskInterface) {
				$this->disks->set($name, $disk);
			}
		}
	}

	// -----------------

	public function has(string $name): bool
	{
		return isset($this->disks[$name]);
	}

	public function add(string $name, array $config): void
	{
		$disk = $this->build($name, $config);

		if ($disk instanceof DiskInterface) {
			$this->disks[$name] = $disk;
		}
	}

	public function get(string|null $name = null): DiskInterface
	{
		if ($name === null) {
			$name = $this->default;
		}

		if (isset($this->disks[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingDiskException("The specified disk could not be found: '$name'."));
		}

		return $this->disks[$name];
	}

	public function getWithDriver(Driver $driver): DiskInterface|null
	{
		return $this->disks->first(function (DiskInterface $disk) use ($driver) {
			return $disk->config->driver === $driver;
		});
	}

	// -----------------

	/**
	 * @returns Map<string, DiskInterface>
	 */
	public function all(): Map
	{
		return $this->disks;
	}

	// -----------------

	public function setDefault(string $name): void
	{
		if (isset($this->disks[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingDiskException("Undefined disks cannot be set as default: '$name'."));
		}
		$this->default = $name;
	}

	public function getDefault(): string
	{
		return $this->default;
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

	protected function build(string $name, array $config): DiskInterface|null
	{
		$config = new DiskConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException($config->get('driver')));
			return null;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new DiskMisconfigurationException("The disk '$name' cannot be used due to a configuration issue."));
			return null;
		}

		return match($config->driver) {
			Driver::Custom => new Custom($name, $config),
			Driver::Local => new Local($name, $config),
			Driver::AsyncS3 => new AsyncS3($name, $config),
			Driver::S3 => new S3($name, $config),
//			Driver::Sftp => new Sftp($name, $config),
			default => null,
		};
	}

}