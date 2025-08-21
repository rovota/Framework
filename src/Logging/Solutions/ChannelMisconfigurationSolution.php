<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging\Solutions;

use Rovota\Framework\Support\Interfaces\Solution;

class ChannelMisconfigurationSolution implements Solution
{

	public function title(): string
	{
		return 'Try the following:';
	}

	public function description(): string
	{
		return 'Ensure that all required parameters are set. For example, channels using the "monolog" driver need parameters to be specified.';
	}

	public function references(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/getting-started/configuration/logging'
		];
	}

}