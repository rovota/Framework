<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use BadMethodCallException;

class Controller
{

	public function __construct()
	{
		$this->configuration();
	}

	public function __call($method, $parameters)
	{
		throw new BadMethodCallException(
			sprintf('Method %s::%s does not exist.', static::class, $method)
		);
	}

	// -----------------

	protected function configuration(): void
	{

	}

}