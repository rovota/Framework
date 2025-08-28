<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Exceptions;

use Exception;
use Rovota\Framework\Kernel\Solutions\MisconfiguredServiceSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class MisconfiguredServiceException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new MisconfiguredServiceSolution();
	}

}