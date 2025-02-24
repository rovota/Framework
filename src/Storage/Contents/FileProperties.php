<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Contents;

use Rovota\Framework\Facades\Storage;
use Rovota\Framework\Storage\Interfaces\DiskInterface;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Validation\ValidationTools;

final class FileProperties extends Config
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

	public string $extension {
		get => $this->string('extension');
		set {
			$this->set('extension', trim($value));
		}
	}

	// -----------------

	public DiskInterface $disk {
		get => $this->get('disk', Storage::disk());
		set {
			$this->set('disk', $value);
		}
	}

	// -----------------

	public int $size {
		get => $this->int('size');
		set {
			$this->set('size', abs($value));
		}
	}

	public string|null $mime_type {
		get => $this->get('mime_type');
		set {
			$value = trim($value);
			if (ValidationTools::mimeTypeExists($value)) {
				$this->set('mime_type', $value);
			}
		}
	}

	public Moment|null $last_modified {
		get => $this->get('last_modified');
		set {
			$this->set('last_modified', $value);
		}
	}

	// -----------------

	public function assign(array $properties): void
	{
		foreach ($properties as $key => $value) {
			if ($key === 'last_modified') {
				$this->set('last_modified', $value instanceof Moment ? $value : moment($value));
				continue;
			}

			if ($key === 'name') {
				$this->set('name', Str::beforeLast($value, '.'));
				$this->set('extension', Str::afterLast($value, '.'));
				continue;
			}

			if ($key === 'path') {
				$this->set('path', Str::trim($value, '/'));
				continue;
			}

			$this->set($key, $value);
		}
	}

}