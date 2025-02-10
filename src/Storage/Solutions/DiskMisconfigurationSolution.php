<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Solutions;

use Rovota\Framework\Support\Interfaces\Solution;

class DiskMisconfigurationSolution implements Solution
{

	public function title(): string
	{
		return 'Try the following:';
	}

	public function description(): string
	{
		return 'Ensure that all required parameters are set. For example, disks using the "s3" driver need connection parameters to be specified.';
	}

	public function references(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/getting-started/configuration/disks'
		];
	}
	
}