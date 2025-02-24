<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Contents\Extensions;

use Rovota\Framework\Storage\Contents\FileProperties;
use Rovota\Framework\Storage\Interfaces\FileContent;

class Standard implements FileContent
{

	public mixed $contents {
		get => $this->contents;
	}

	// -----------------

	public function __construct(mixed $contents)
	{
		$this->contents = $contents;
	}

	// -----------------

	public static function accepts(mixed $data, FileProperties $properties): bool
	{
		return true;
	}

}