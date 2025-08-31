<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Exceptions;

use Exception;

class UnsupportedDriverException extends Exception
{

	public function __construct(string $driver)
	{
		parent::__construct("The selected driver '$driver' is not supported.");
	}

}