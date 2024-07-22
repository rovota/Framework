<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Structures;

use Rovota\Framework\Support\Str;

class Config extends Bucket
{

	public function __get(string $name)
	{
		return $this->{'get'.Str::pascal($name)}();
	}
	
	public function __set(string $name, $value): void
	{
		$method = 'set'.Str::pascal($name);
		if (method_exists($this, $method)) {
			$this->$method($value);
		} else {
			$this->set($name, $value);
		}
	}

}