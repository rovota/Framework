<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database;

use Rovota\Framework\Database\Enums\Driver;
use Rovota\Framework\Support\Config;

final class ConnectionConfig extends Config
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
		get => $this->get('label', 'Unnamed Connection');
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