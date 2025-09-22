<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client;

use Rovota\Framework\Http\Client\Enums\Driver;
use Rovota\Framework\Support\Config;

final class ClientConfig extends Config
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
		get => $this->string('label', 'Unnamed Connector');
		set {
			$this->set('label', trim($value));
		}
	}

	// -----------------

	public string $url {
		get => $this->string('url');
		set {
			$this->set('url', trim($value));
		}
	}

	public array $timeouts {
		get => array_merge(['connection' => 10, 'request' => 30], $this->array('timeouts'));
		set {
			foreach ($value as $name => $number) {
				$this->set('timeouts.' . $name, $number);
			}
		}
	}

	public Config $options {
		get => new Config($this->array('options'));
		set {
			$this->set('options', $value->toArray());
		}
	}

	// -----------------

	public function isValid(): bool
	{
		return true;
	}

}