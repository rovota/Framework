<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Routing\Enums\Scheme;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Support\Str;

class UrlObjectConfig extends Config
{

	public Scheme $scheme {
		get => $this->get('scheme') ?? Scheme::Https;
		set {
			$this->set('scheme', $value);
		}
	}

	// -----------------

	public string|null $subdomain {
		get {
			$domain = $this->domain;

			if (Str::occurrences($domain, '.') > 1) {
				return Str::after($domain, '.');
			}

			return null;
		}
		set {
			if ($this->get('domain') === null) {
				$this->domain = $this->getSanitizedHost();
			}

			if ($value === null && Str::occurrences($this->domain, '.') > 1) {
				$this->domain = Str::after($this->domain, '.');
				return;
			}

			$value = trim($value);

			if (mb_strlen($value) > 0) {
				if (Str::occurrences($this->domain, '.') > 1) {
					$this->domain = Str::after($this->domain, '.');
				}
				$this->domain = sprintf('%s.%s', $value, $this->domain);
			}
		}
	}

	public string $domain {
		get => $this->string('domain', $this->getSanitizedHost());
		set {
			$value = Str::after(trim($value), 'www.');

			if (mb_strlen($value) === 0 || $value === '-') {
				$this->set('domain', $this->getSanitizedHost());
				return;
			}

			$this->set('domain', $value);
		}
	}

	public int $port {
		get => $this->int('port', (int)Framework::environment()->server->get('SERVER_PORT'));
		set {
			$this->set('port', $value);
		}
	}

	// -----------------

	public string $path {
		get => Str::start($this->string('path'), '/');
		set {
			$value = trim($value, ' /');

			if (mb_strlen($value) > 0) {
				$this->set('path', $value);
			}
		}
	}

	public array $parameters {
		get => $this->array('parameters');
		set {
			if (empty($value)) {
				$this->remove('parameters');
				return;
			}

			foreach ($value as $name => $data) {
				$name = 'parameters.' . strtolower(trim($name));
				if ($data === null) {
					$this->remove($name);
					continue;
				}
				$this->set($name, $data);
			}
		}
	}

	public string|null $fragment {
		get => $this->get('fragment');
		set {
			if ($value === null) {
				$this->remove('fragment');
				return;
			}

			$value = trim($value);

			if (mb_strlen($value) > 0) {
				$this->set('fragment', $value);
			}
		}
	}

	// -----------------

	protected function getSanitizedHost(): string
	{
		return Str::after(Framework::environment()->server->get('HTTP_HOST') ?? '', 'www.');
	}

}