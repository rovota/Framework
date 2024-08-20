<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Rovota\Framework\Structures\Bucket;

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

	// -----------------

	public static function load(string $path, bool $source = false): static
	{
		$path = Str::finish($path, '.php');
		$config = $source ? Internal::sourceFile($path) : Internal::projectFile($path);

		return new static($config);
	}

}