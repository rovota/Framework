<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\ReadOnly\ReadOnlyFilesystemAdapter;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Storage\Interfaces\DiskInterface;
use Rovota\Framework\Storage\Traits\CompressionFunctions;
use Rovota\Framework\Storage\Traits\DiskFunctions;

abstract class Disk implements DiskInterface
{
	use DiskFunctions, CompressionFunctions;

	// -----------------

	public string $name {
		get => $this->name;
	}

	public DiskConfig $config {
		get => $this->config;
	}

	public FilesystemAdapter $adapter {
		get => $this->adapter;
	}

	public Filesystem $flysystem {
		get => $this->flysystem;
	}

	public UrlObject $url {
		get => UrlObject::from($this->config->domain . '/' . $this->config->root);
	}

	// -----------------

	public function __construct(string $name, FilesystemAdapter $adapter, DiskConfig $config)
	{
		$this->name = $name;
		$this->config = $config;

		$this->adapter = $this->config->read_only ? new ReadOnlyFilesystemAdapter($adapter) : $adapter;
		$this->flysystem = new Filesystem($this->adapter);
	}

	// -----------------

	public function __toString(): string
	{
		return $this->name;
	}

	// -----------------

	public function isDefault(): bool
	{
		return StorageManager::instance()->getDefault() === $this->name;
	}

}