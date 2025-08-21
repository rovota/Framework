<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Throttling;

use Closure;
use Rovota\Framework\Caching\CacheManager;
use Rovota\Framework\Caching\Interfaces\CacheInterface;
use Rovota\Framework\Http\Throttling\Enums\IdentifierType;
use Rovota\Framework\Http\Throttling\Enums\PeriodType;
use Rovota\Framework\Support\Config;

/**
 * @property CacheInterface $cache
 */
class LimitConfig extends Config
{

	public CacheInterface $cache {
		get => $this->get('cache', CacheManager::instance()->get());
		set {
			$this->set('cache', $value);
		}
	}

	// -----------------

	public int $limit {
		get => $this->int('limit', PHP_INT_MAX);
		set {
			$this->set('limit', abs($value));
		}
	}

	// -----------------

	public string $identifier {
		get => $this->string('identifier', '---');
		set {
			$this->set('identifier', $value);
		}
	}

	public IdentifierType $identifier_type {
		get => IdentifierType::tryFrom($this->string('identifier_type')) ?? IdentifierType::Global;
		set {
			if ($value instanceof IdentifierType) {
				$this->set('identifier_type', $value->name);
			}
		}
	}

	// -----------------

	public int $period {
		get => $this->int('period', 1);
		set {
			$this->set('period', abs($value));
		}
	}

	public int $period_in_seconds {
		get => match ($this->period_type) {
			PeriodType::Second => $this->period,
			PeriodType::Minute => $this->period * 60,
			PeriodType::Hour => $this->period * 3600,
			PeriodType::Day => $this->period * 86400,
			PeriodType::Week => $this->period * 7 * 86400,
		};
	}

	public PeriodType $period_type {
		get => PeriodType::tryFrom($this->string('period_type')) ?? PeriodType::Second;
		set {
			if ($value instanceof PeriodType) {
				$this->set('period_type', $value->value);
			}
		}
	}

	// -----------------

	public Closure $response {
		get {
			if ($this->missing('response')) {
				return fn() => 429;
			}
			return $this->get('response');
		}
		set {
			$this->set('response', $value);
		}
	}

}