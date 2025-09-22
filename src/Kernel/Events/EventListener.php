<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Events;

use Rovota\Framework\Kernel\Events\Interfaces\Event;

abstract class EventListener
{

	abstract public function handle(Event $event): void;

}