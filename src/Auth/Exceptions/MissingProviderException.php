<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth\Exceptions;

use Exception;
use Rovota\Framework\Auth\Solutions\MissingProviderSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class MissingProviderException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new MissingProviderSolution();
	}

}