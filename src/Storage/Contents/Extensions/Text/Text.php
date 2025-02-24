<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Contents\Extensions\Text;

use Rovota\Framework\Storage\Contents\FileProperties;
use Rovota\Framework\Storage\Interfaces\FileContent;
use Stringable;

class Text implements FileContent, Stringable
{

	public string $contents {
		get => $this->contents;
	}

	// -----------------

	public function __construct(Stringable|string $contents)
	{
		$this->contents = $contents;
	}

	public function __toString(): string
	{
		return $this->contents;
	}

	// -----------------

	public static function accepts(mixed $data, FileProperties $properties): bool
	{
		if ($data instanceof Stringable || is_string($data)) {
			return true;
		}

		return str_contains($properties->mime_type, 'text/');
	}

}