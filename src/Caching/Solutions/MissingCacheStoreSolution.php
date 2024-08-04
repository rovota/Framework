<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Solutions;

use Rovota\Framework\Support\Interfaces\Solution;

class MissingCacheStoreSolution implements Solution
{

	public function title(): string
	{
		return 'Try the following:';
	}

	public function description(): string
	{
		return 'Ensure that you have a cache configured using the name specified. You may have made a spelling error.';
	}

	public function references(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/getting-started/configuration/caches'
		];
	}
	
}