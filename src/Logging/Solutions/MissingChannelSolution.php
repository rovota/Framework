<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging\Solutions;

use Rovota\Framework\Support\Interfaces\Solution;

class MissingChannelSolution implements Solution
{

	public function title(): string
	{
		return 'Try the following:';
	}

	public function description(): string
	{
		return 'Ensure that you have a channel configured using the name specified. You may have made a spelling error.';
	}

	public function references(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/getting-started/configuration/logging'
		];
	}
	
}