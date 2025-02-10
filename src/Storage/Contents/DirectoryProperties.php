<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Contents;

use Rovota\Framework\Storage\Interfaces\DiskInterface;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Support\Str;

final class DirectoryProperties extends Config
{

	public string $name {
		get => $this->string('name');
		set {
			$this->set('name', trim($value));
		}
	}

	public string $path {
		get => $this->string('path');
		set {
			$this->set('path', trim($value, '/'));
		}
	}

	// -----------------

	public DiskInterface $disk {
		get => $this->get('disk');
		set {
			$this->set('disk', $value);
		}
	}

	// -----------------

	public function assign(array $properties): void
	{
		foreach ($properties as $key => $value) {
			if ($key === 'path') {
				$this->set('path', Str::trim($value, '/'));
				continue;
			}

			$this->set($key, $value);
		}
	}

}