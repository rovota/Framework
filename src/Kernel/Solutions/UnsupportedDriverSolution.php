<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Solutions;

use Rovota\Framework\Support\Interfaces\Solution;

class UnsupportedDriverSolution implements Solution
{

	public function title(): string
	{
		return 'Supported Drivers';
	}

	public function description(): string
	{
		return 'Make sure you have the latest version installed, and that the driver name is spelled correctly.';
	}

	public function references(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/getting-started/configuration'
		];
	}
	
}