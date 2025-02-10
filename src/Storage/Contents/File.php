<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Contents;

use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Storage\Traits\FileFunctions;
use Stringable;

class File implements Stringable
{
	use FileFunctions;

	// -----------------

	public string|null $contents {
		get => $this->contents;
	}

	public FileProperties $properties {
		get => $this->properties;
	}

	public UrlObject $url {
		get {
			$path = sprintf('%s/%s.%s', $this->properties->path, $this->properties->name, $this->properties->extension);
			return UrlObject::from($this->properties->disk->url . $path);
		}
	}

	protected bool $modified = false;

	// -----------------

	public function __construct(mixed $contents, array $properties, bool $modified = false)
	{
		$this->contents = $contents;
		$this->modified = $modified;

		$this->properties = new FileProperties();
		$this->properties->assign($properties);
	}

	public function __toString(): string
	{
		return $this->contents;
	}

}