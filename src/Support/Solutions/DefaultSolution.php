<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Solutions;

use Rovota\Framework\Support\Interfaces\Solution;

class DefaultSolution implements Solution
{

	public function title(): string
	{
		return 'Solution';
	}

	public function description(): string
	{
		return 'Check whether the spelling is correct, and make sure that all classes can be found.';
	}

	public function references(): array
	{
		return [];
	}

}