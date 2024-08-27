<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Path;

/**
 * @internal
 */
final class RegistryManager extends ServiceProvider
{

	protected Bucket $entries;

	// -----------------

	public function __construct()
	{
		$file = require Path::toProjectFile('config/registry.php');

		$this->entries = new Bucket($file);
	}

	// -----------------

	public function import(array $entries): void
	{
		$this->entries->import($entries);
	}

	public function entries(): Bucket
	{
		return $this->entries;
	}

}