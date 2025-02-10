<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Exceptions;

use Exception;
use Rovota\Framework\Storage\Solutions\MissingDiskSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class MissingDiskException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new MissingDiskSolution();
	}

}