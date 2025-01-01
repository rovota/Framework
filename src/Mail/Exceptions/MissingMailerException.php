<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Exceptions;

use Exception;
use Rovota\Framework\Mail\Solutions\MissingMailerSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class MissingMailerException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new MissingMailerSolution();
	}

}