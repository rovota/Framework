<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Exceptions;

use Exception;
use Rovota\Framework\Storage\Solutions\DiskMisconfigurationSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class DiskMisconfigurationException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new DiskMisconfigurationSolution();
	}

}