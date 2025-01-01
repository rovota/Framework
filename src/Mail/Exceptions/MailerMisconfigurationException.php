<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Exceptions;

use Exception;
use Rovota\Framework\Mail\Solutions\MailerMisconfigurationSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class MailerMisconfigurationException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new MailerMisconfigurationSolution();
	}

}