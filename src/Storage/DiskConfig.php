<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage;

use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Storage\Enums\Driver;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Support\Str;

final class DiskConfig extends Config
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
		get => $this->string('label', 'Unnamed Disk');
		set {
			$this->set('label', trim($value));
		}
	}

	// -----------------

	public string $root {
		get => mb_trim($this->string('root'), '/');
		set {
			$this->set('root', mb_trim($value));
		}
	}

	public string $domain {
		get {
			$domain = $this->get('domain');
			$fallback = Framework::environment()->server->get('HTTP_HOST');

			if (is_array($domain)) {
				return $domain[Framework::environment()->type->value] ?? $fallback;
			}

			return $domain ?? $fallback;
		}
		set {
			$this->set('domain', $value);
		}
	}

	public string|null $adapter {
		get => $this->get('adapter');
		set {
			$this->set('adapter', $value);
		}
	}

	public string $visibility {
		get => $this->get('visibility', 'private');
		set {
			$this->set('visibility', $value);
		}
	}

	public Config $parameters {
		get => new Config($this->array('parameters'));
		set {
			$this->set('parameters', $value->toArray());
		}
	}

	// -----------------

	public bool $read_only {
		get => $this->bool('read_only');
		set {
			$this->set('read_only', $value);
		}
	}

	public bool $auto_connect {
		get => $this->bool('auto_connect');
		set {
			$this->set('auto_connect', $value);
		}
	}

	// -----------------

	public function isValid(): bool
	{
		return true;
	}

}