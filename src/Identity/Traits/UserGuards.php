<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Identity\Traits;

use Rovota\Framework\Identity\Models\Guard;
use Rovota\Framework\Structures\Extensions\GuardBag;

trait UserGuards
{

	protected GuardBag|null $guard_entities = null;

	/**
	 * @var GuardBag<string, Guard>
	 */
	public GuardBag $guards {
		get {
			if ($this->guard_entities === null) {
				$this->loadGuards();
			}
			return $this->guard_entities;
		}
		set {
			$this->guard_entities = $value;
		}
	}

	// -----------------

	protected function loadGuards(): void
	{
		$this->guard_entities = new GuardBag();

		$guards = Guard::where(['user_id' => $this->id])->get()->keyBy(function (Guard $guard) {
			return $guard->type->value;
		});

		foreach ($guards as $type => $guard) {
			$this->guard_entities->set($type, $guard);
		}
	}

}