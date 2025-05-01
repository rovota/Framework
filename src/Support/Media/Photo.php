<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Media;

use GdImage;
use Rovota\Framework\Support\Media\Traits\PhotoModifiers;
use Stringable;

final class Photo implements Stringable
{
	use PhotoModifiers;

	// -----------------

	public GdImage $data {
		get => $this->data;
	}

	// -----------------

	public PhotoProperties $properties {
		get => $this->properties;
	}

	// -----------------

	public function __construct(string $data, array $properties = [])
	{
		$this->data = imagecreatefromstring($data);

		$this->properties = new PhotoProperties([
			'width' => imagesx($this->data),
			'height' => imagesy($this->data),
		]);

		$this->properties->assign($properties);

		if ($this->properties->mime_type = 'image/png') {
			imagealphablending($this->data, false);
			imagesavealpha($this->data, true);
		}
	}

	public function __toString(): string
	{
		return $this->getImageAsString();
	}

	// -----------------

	protected function getImageAsString(): string
	{
		ob_start();

		match ($this->properties->mime_type) {
			'image/gif' => imagegif($this->data),
			'image/bmp' => imagebmp($this->data),
			'image/png' => imagepng($this->data, quality: $this->properties->quality),
			'image/avif' => imageavif($this->data, quality: $this->properties->quality),
			'image/webp' => imagewebp($this->data, quality: $this->properties->quality),
			default => imagejpeg($this->data, quality: $this->properties->quality),
		};

		$result = ob_get_clean();

		return $result !== false ? $result : '';
	}

}