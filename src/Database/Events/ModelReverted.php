<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Events;

use Rovota\Framework\Database\Model\Model;
use Rovota\Framework\Kernel\Events\Interfaces\Event;
use Rovota\Framework\Kernel\Events\Traits\Dispatchable;

class ModelReverted implements Event
{
	use Dispatchable;

	// -----------------

	public function __construct(
		public Model $model
	)
	{
	}
}