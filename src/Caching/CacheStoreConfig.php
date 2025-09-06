<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching;

use Closure;
use Rovota\Framework\Caching\Enums\Driver;
use Rovota\Framework\Support\Config;

final class CacheStoreConfig extends Config
{

	public Driver|null $driver {
		get => Driver::tryFrom($this->string('driver'));
		set {
			if ($value instanceof Driver) {
				$this->set('driver', $value->name);
			}
		}
	}

	public string $label {
		get => $this->string('label', 'Unnamed Cache');
		set {
			$this->set('label', trim($value));
		}
	}

	// -----------------

	public int $retention {
		get => $this->int('retention');
		set {
			$this->set('retention', abs($value));
		}
	}

	public Closure|string|null $scope {
		get => $this->get('scope');
		set {
			$this->set('scope', $value);
		}
	}

	public Config $parameters {
		get => new Config($this->array('parameters'));
		set {
			$this->set('parameters', $value->toArray());
		}
	}

	// -----------------

	public function isValid(): bool
	{
		return true;
	}

}