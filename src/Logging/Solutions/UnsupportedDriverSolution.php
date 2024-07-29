<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging\Solutions;

use Rovota\Framework\Support\Interfaces\Solution;

class UnsupportedDriverSolution implements Solution
{
	public function title(): string
	{
		return 'Try the following:';
	}

	public function description(): string
	{
		return 'Ensure you have the latest version of Core installed, all dependencies for this driver are present and that the driver name is spelled correctly.';
	}

	public function references(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/getting-started/configuration/logging'
		];
	}
	
}