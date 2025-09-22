<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Config;

class RouteConfig extends Config
{

	public array $methods {
		get => $this->array('methods');
		set {
			foreach ($value as $key => $method) {
				if (Arr::contains(Router::ACCEPTED_METHODS, $method) === false) {
					unset($value[$key]);
				}
			}

			$this->set('methods', $value);
		}
	}

	// -----------------

	public mixed $target {
		get => $this->get('target');
		set {
			$this->set('target', $value);
		}
	}

	public string $path {
		get => $this->string('path', '/');
		set {
			$this->set('path', $value);
		}
	}

	// -----------------

	public array $context {
		get => $this->array('context');
		set {
			$this->set('context', $value);
		}
	}

}