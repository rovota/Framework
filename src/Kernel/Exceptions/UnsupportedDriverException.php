<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Exceptions;

use Exception;
use Rovota\Framework\Kernel\Solutions\UnsupportedDriverSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class UnsupportedDriverException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new UnsupportedDriverSolution();
	}

}