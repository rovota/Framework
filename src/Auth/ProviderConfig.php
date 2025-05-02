<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth;

use Rovota\Framework\Auth\Enums\Driver;
use Rovota\Framework\Support\Config;

final class ProviderConfig extends Config
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
		get => $this->string('label', 'Unnamed Provider');
		set {
			$this->set('label', trim($value));
		}
	}

	// -----------------

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