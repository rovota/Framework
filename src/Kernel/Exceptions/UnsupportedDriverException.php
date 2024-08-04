<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Exceptions;

use Exception;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class UnsupportedDriverException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new UnsupportedDriverSolution();
	}

}