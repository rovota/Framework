<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging\Exceptions;

use Exception;
use Rovota\Framework\Logging\Solutions\ChannelMisconfigurationSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class ChannelMisconfigurationException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new ChannelMisconfigurationSolution();
	}

}