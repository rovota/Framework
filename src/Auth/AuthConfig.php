<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth;

use Rovota\Framework\Support\Config;

class AuthConfig extends Config
{

	public string $default {
		get => $this->string('default', array_key_first($this->array('providers')) ?? '---');
		set {
			$this->set('default', $value);
		}
	}

	public array $providers {
		get => $this->array('providers');
		set {
			$this->set('providers', $value);
		}
	}

}