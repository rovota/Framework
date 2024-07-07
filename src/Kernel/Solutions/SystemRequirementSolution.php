<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Solutions;

use Rovota\Framework\Support\Interfaces\Solution;

class SystemRequirementSolution implements Solution
{

	public function getTitle(): string
	{
		return 'Incompatibility';
	}

	public function getDescription(): string
	{
		return 'You need to make sure your PHP version and extensions are compatible.';
	}

	public function getDocumentationLinks(): array
	{
		return ['System Requirements' => 'https://docs.rovota.com'];
	}
}