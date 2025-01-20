<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging;

use Rovota\Framework\Logging\Enums\Driver;
use Rovota\Framework\Support\Config;

final class ChannelConfig extends Config
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
		get => $this->string('label', 'Unnamed Channel');
		set {
			$this->set('label', trim($value));
		}
	}

	// -----------------

	public array $channels {
		get => $this->array('channels');
	}

	public string|null $handler {
		get => $this->get('handler');
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