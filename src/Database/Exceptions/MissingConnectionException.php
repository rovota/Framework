<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Exceptions;

use Exception;
use Rovota\Framework\Database\Solutions\MissingConnectionSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class MissingConnectionException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new MissingConnectionSolution();
	}

}