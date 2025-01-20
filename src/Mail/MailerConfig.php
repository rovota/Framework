<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail;

use Rovota\Framework\Mail\Enums\Driver;
use Rovota\Framework\Support\Config;

final class MailerConfig extends Config
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
		get => $this->string('label', 'Unnamed Mailer');
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