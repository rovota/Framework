<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Config;

/**
 * @property array $methods
 * @property mixed $target
 * @property string $path
 *
 * @property array $context
 */
class RouteConfig extends Config
{

	public function getMethods(): array
	{
		return $this->array('methods');
	}

	public function setMethods(array $methods): void
	{
		foreach ($methods as $key => $method) {
			if (Arr::contains(Router::ACCEPTED_METHODS, $method) === false) {
				unset($methods[$key]);
			}
		}

		$this->set('methods', $methods);
	}

	// -----------------

	public function getTarget(): mixed
	{
		return $this->get('target');
	}

	public function setTarget(mixed $target): void
	{
		$this->set('target', $target);
	}

	// -----------------

	public function getPath(): mixed
	{
		return $this->get('path');
	}

	public function setPath(mixed $path): void
	{
		$this->set('path', $path);
	}

	// -----------------

	public function getContext(): mixed
	{
		return $this->get('context');
	}

	public function setContext(mixed $context): void
	{
		$this->set('context', $context);
	}

}