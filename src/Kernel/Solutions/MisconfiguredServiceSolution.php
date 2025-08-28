<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Solutions;

use Rovota\Framework\Support\Interfaces\Solution;

class MisconfiguredServiceSolution implements Solution
{

	public function title(): string
	{
		return 'Try the following:';
	}

	public function description(): string
	{
		return 'Ensure that all required parameters are set. For example, some drivers may need different parameters.';
	}

	public function references(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/services'
		];
	}

}