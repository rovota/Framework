<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Exceptions;

use Exception;
use Rovota\Framework\Caching\Solutions\MissingCacheStoreSolution;
use Rovota\Framework\Support\Interfaces\ProvidesSolution;
use Rovota\Framework\Support\Interfaces\Solution;

class MissingCacheStoreException extends Exception implements ProvidesSolution
{

	public function solution(): Solution
	{
		return new MissingCacheStoreSolution();
	}

}