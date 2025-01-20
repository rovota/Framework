<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database;

use Rovota\Framework\Support\Config;

class DatabaseConfig extends Config
{

	public string $default {
		get => $this->string('default', array_key_first($this->array('connections')) ?? '---');
		set {
			$this->set('default', $value);
		}
	}

	public array $connections {
		get => $this->array('connections');
		set {
			$this->set('connections', $value);
		}
	}

}