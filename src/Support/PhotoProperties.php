<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Rovota\Framework\Validation\ValidationTools;

final class PhotoProperties extends Config
{

	public int $width {
		get => $this->int('width');
		set {
			$this->set('width', abs($value));
		}
	}

	public int $height {
		get => $this->int('height');
		set {
			$this->set('height', abs($value));
		}
	}

	// -----------------

	public string|null $mime_type {
		get => $this->get('mime_type');
		set {
			$value = trim($value);
			if (ValidationTools::mimeTypeExists($value)) {
				$this->set('mime_type', $value);
			}
		}
	}

	public int $quality {
		get => $this->int('quality', -1);
		set {
			$this->set('quality', abs($value));
		}
	}

	// -----------------

	public function assign(array $properties): void
	{
		foreach ($properties as $key => $value) {
			$this->set($key, $value);
		}
	}

}