<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging\Exceptions;

use Exception;
use Rovota\Framework\Logging\Solutions\UnsupportedDriverSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class UnsupportedDriverException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new UnsupportedDriverSolution();
	}

}