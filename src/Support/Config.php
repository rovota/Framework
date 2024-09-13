<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Rovota\Framework\Structures\Bucket;

class Config extends Bucket
{

	protected static string|null $file = null;

	// -----------------

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

	public static function load(string $path): static
	{
		$path = Str::finish($path, '.php');
		$path = Path::toProjectFile($path);

		if (file_exists($path)) {
			$config = include $path;
		}

		return new static($config ?? []);
	}

}