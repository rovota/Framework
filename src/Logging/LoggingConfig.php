<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging;

use Rovota\Framework\Support\Config;

class LoggingConfig extends Config
{

	public string $default {
		get => $this->string('default', array_key_first($this->array('channels')) ?? '---');
		set {
			$this->set('default', $value);
		}
	}

	public array $channels {
		get => $this->array('channels');
		set {
			$this->set('channels', $value);
		}
	}

}