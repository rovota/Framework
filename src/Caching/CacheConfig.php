<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching;

use Rovota\Framework\Support\Config;

class CacheConfig extends Config
{

	public string $default {
		get => $this->string('default', array_key_first($this->array('stores')) ?? '---');
		set {
			$this->set('default', $value);
		}
	}

	public array $stores {
		get => $this->array('stores');
		set {
			$this->set('stores', $value);
		}
	}

}