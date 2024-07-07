<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Exceptions;

use Exception;
use Rovota\Framework\Kernel\Solutions\SystemRequirementSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class SystemRequirementException extends Exception implements ProvidesSolution
{

	public function getSolution(): Solution
	{
		return new SystemRequirementSolution();
	}

}