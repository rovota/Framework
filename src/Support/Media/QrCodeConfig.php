<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Media;

use Rovota\Framework\Support\Config;
use Rovota\Framework\Support\Str;

final class QrCodeConfig extends Config
{

	public string $data {
		get => $this->string('data', '-');
	}

	public string $format {
		get => $this->string('format', 'svg'); // PNG, GIF, JPEG, SVG, EPS
	}

	// -----------------

	public string $margin {
		get => $this->int('margin', 4);
		set {
			$this->set('margin', limit(abs($value), 0, 100));
		}
	}

	// -----------------

	public string $height {
		get => $this->int('height', 200);
		set {
			$this->set('height', abs($value));
		}
	}

	public string $width {
		get => $this->int('width', 200);
		set {
			$this->set('width', abs($value));
		}
	}

	public string $size {
		get => $this->height.'x'.$this->width;
	}

	// -----------------

	public string $background {
		get => $this->string('background', 'FFFFFF');
		set {
			$this->set('background', Str::remove($value, '#'));
		}
	}

	public string $foreground {
		get => $this->string('foreground', '000000');
		set {
			$this->set('foreground', Str::remove($value, '#'));
		}
	}

}