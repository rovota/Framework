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

	protected Bucket $events;

	// -----------------

	public function __construct()
	{
		$this->events = new Bucket();
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
		if ($this->events->missing($event)) {
			return new Bucket();
		}

		return $this->events->get($event);
	}

	// -----------------

	public function dispatchEvent(Event $event): void
	{
		if ($this->events->missing($event::class)) {
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
		if ($this->events->missing($event)) {
			$this->events->set($event, new Bucket());
		}

		$group = $this->events->get($event);
		$group->set($listener::class, $listener);

		$this->events->set($event, $group);
	}

}