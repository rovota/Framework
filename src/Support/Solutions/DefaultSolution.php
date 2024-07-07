<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Solutions;

use Rovota\Framework\Support\Interfaces\Solution;

class DefaultSolution implements Solution
{

	public function getTitle(): string
	{
		return 'Solution';
	}

	public function getDescription(): string
	{
		return 'Check whether the spelling is correct, and make sure that all classes can be found.';
	}

	public function getDocumentationLinks(): array
	{
		return [];
	}

}