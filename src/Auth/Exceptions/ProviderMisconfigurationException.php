<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth\Exceptions;

use Exception;
use Rovota\Framework\Auth\Solutions\ProviderMisconfigurationSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class ProviderMisconfigurationException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new ProviderMisconfigurationSolution();
	}

}