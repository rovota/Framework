<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Contents;

use Rovota\Framework\Storage\Disk;
use Rovota\Framework\Support\Config;

final class DirectoryProperties extends Config
{

	public string $name {
		get => $this->string('name');
		set {
			$this->set('name', mb_trim($value));
		}
	}

	public string $path {
		get => $this->string('path');
		set {
			$this->set('path', mb_trim($value, '/'));
		}
	}

	// -----------------

	public Disk $disk {
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
				$this->set('path', mb_trim($value, '/'));
				continue;
			}

			$this->set($key, $value);
		}
	}

}