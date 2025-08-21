<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth\Solutions;

use Rovota\Framework\Support\Interfaces\Solution;

class ProviderMisconfigurationSolution implements Solution
{

	public function title(): string
	{
		return 'Try the following:';
	}

	public function description(): string
	{
		return 'Ensure that all required parameters are set. For example, providers using the "standard" driver needs a database connection.';
	}

	public function references(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/getting-started/configuration/providers'
		];
	}

}