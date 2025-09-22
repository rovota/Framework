<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Drivers;

use League\Flysystem\Local\LocalFilesystemAdapter;
use Rovota\Framework\Storage\Disk;
use Rovota\Framework\Storage\DiskConfig;

class Local extends Disk
{

	public function __construct(string $name, DiskConfig $config)
	{
		$adapter = new LocalFilesystemAdapter($config->root);

		parent::__construct($name, $adapter, $config);
	}

}