<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Interfaces;

use Rovota\Framework\Structures\Bucket;

interface ContextAware
{

	public Bucket $context {
		get;
		set;
	}

}