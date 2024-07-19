<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Traits;

use Rovota\Framework\Support\Arr;

trait EnumHelpers
{

	// TODO: Implement Arr class

	public function isAny(array $items): bool
	{
		return Arr::containsAny($items, [$this]);
	}

	public function isNone(array $items): bool
	{
		return Arr::containsAny($items, [$this]) === false;
	}

}