<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Contents;

use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Storage\Interfaces\DirectoryInterface;
use Rovota\Framework\Storage\Traits\DirectoryFunctions;
use Stringable;

class Directory implements DirectoryInterface, Stringable
{
	use DirectoryFunctions;

	// -----------------

	public DirectoryProperties $properties {
		get => $this->properties;
	}

	public UrlObject $url {
		get {
			$path = sprintf('%s/%s', $this->properties->path, $this->properties->name);
			return UrlObject::from($this->properties->disk->url . $path);
		}
	}

	// -----------------

	public function __construct(array $properties)
	{
		$this->properties = new DirectoryProperties();
		$this->properties->assign($properties);
	}

	public function __toString(): string
	{
		return $this->properties->name;
	}

}