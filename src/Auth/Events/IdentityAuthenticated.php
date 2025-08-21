<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth\Events;

use Rovota\Framework\Identity\Models\User;
use Rovota\Framework\Kernel\Events\Interfaces\Event;
use Rovota\Framework\Kernel\Events\Traits\Dispatchable;

class IdentityAuthenticated implements Event
{
	use Dispatchable;

	// -----------------

	public function __construct(
		public User $user
	)
	{
	}
}