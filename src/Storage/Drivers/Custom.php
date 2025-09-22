<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Drivers;

use Rovota\Framework\Storage\Disk;
use Rovota\Framework\Storage\DiskConfig;

class Custom extends Disk
{

	public function __construct(string $name, DiskConfig $config)
	{
		$adapter = new $config->adapter(...$config->parameters);

		parent::__construct($name, $adapter, $config);
	}

}