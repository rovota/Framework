<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Identity\Traits;

use Rovota\Framework\Facades\Cache;
use Rovota\Framework\Support\Moment;

trait UserActivity
{

	public Moment|null $last_active {
		get {
			return Cache::get('last_active_hit:' . $this->id, $this->getAttribute('last_active') ?? $this->created);
		}
		set {
			$key = 'last_active_hit:' . $this->id;
			$moment = moment($value);

			if (Cache::has($key) === false) {
				Cache::set($key, $moment, 300);

				$this->setAttribute('last_active', $moment);
				$this::class::update(['last_active' => $moment])->where($this->config->primary_key, $this->{$this->config->primary_key})->submit();
			}
		}
	}

	// -----------------

	public function isOnline(int $minutes = 5): bool
	{
		return $this->last_active->diffInMinutes() <= $minutes;
	}

	public function isActive(int $days = 365): bool
	{
		return $this->last_active->diffInDays() <= $days;
	}

}