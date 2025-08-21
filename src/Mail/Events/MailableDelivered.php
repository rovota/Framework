<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Events;

use Rovota\Framework\Kernel\Events\Interfaces\Event;
use Rovota\Framework\Kernel\Events\Traits\Dispatchable;
use Rovota\Framework\Mail\Mailable;

class MailableDelivered implements Event
{
	use Dispatchable;

	// -----------------

	public function __construct(
		public Mailable $mailable
	)
	{
	}
}