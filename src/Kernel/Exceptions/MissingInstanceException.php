<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Exceptions;

use Exception;
use Rovota\Framework\Kernel\Solutions\MissingInstanceSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class MissingInstanceException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new MissingInstanceSolution();
	}

}