<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security\Exceptions;

use Exception;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;
use Rovota\Framework\Support\Solutions\DefaultSolution;

class EncryptionException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new DefaultSolution();
	}

}