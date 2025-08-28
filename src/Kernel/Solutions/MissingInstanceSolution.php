<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Solutions;

use Rovota\Framework\Support\Interfaces\Solution;

class MissingInstanceSolution implements Solution
{

	public function title(): string
	{
		return 'Try the following:';
	}

	public function description(): string
	{
		return 'Ensure that an instance has been configured using the name specified. You may have made a spelling error.';
	}

	public function references(): array
	{
		return [];
	}

}