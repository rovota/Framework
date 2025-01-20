<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Events;

use Rovota\Framework\Kernel\Events\Interfaces\Event;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Structures\Bucket;

/**
 * @internal
 */
final class EventManager extends ServiceProvider
{

	protected Bucket $listeners;

	// -----------------

	public function __construct()
	{
		$this->listeners = new Bucket();
	}

	// -----------------

	public function addListener(string $event, EventListener|string $listener): EventListener
	{
		if (is_string($listener)) {
			$listener = new $listener();
		}

		$this->addListenerToEvent($event, $listener);
		return $listener;
	}

	// -----------------

	public function getListeners(string $event): Bucket
	{
		if ($this->listeners->missing($event)) {
			return new Bucket();
		}

		return $this->listeners->get($event);
	}

	// -----------------

	public function dispatchEvent(Event $event): void
	{
		if ($this->listeners->missing($event::class)) {
			return;
		}

		$listeners = $this->getListeners($event::class);

		foreach ($listeners as $listener) {
			if ($listener instanceof EventListener) {
				$listener->handle($event);
			}
		}
	}

	// -----------------

	protected function addListenerToEvent(string $event, EventListener $listener): void
	{
		if ($this->listeners->missing($event)) {
			$this->listeners->set($event, new Bucket());
		}

		$group = $this->listeners->get($event);
		$group->set($listener::class, $listener);

		$this->listeners->set($event, $group);
	}

}