<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage;

use Rovota\Framework\Support\Config;

class StorageConfig extends Config
{

	public string $default {
		get => $this->string('default', array_key_first($this->array('disks')) ?? '---');
		set {
			$this->set('default', $value);
		}
	}

	public array $disks {
		get => $this->array('disks');
		set {
			$this->set('disks', $value);
		}
	}

}