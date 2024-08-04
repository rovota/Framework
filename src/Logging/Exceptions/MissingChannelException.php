<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging\Exceptions;

use Exception;
use Rovota\Framework\Logging\Solutions\MissingChannelSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class MissingChannelException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new MissingChannelSolution();
	}

}