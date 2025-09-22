<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Media\Traits;

use GdImage;

trait PhotoModifiers
{

	public function type(string $type): static
	{
		$this->properties->mime_type = $type;
		return $this;
	}

	public function quality(int $quality): static
	{
		$this->properties->quality = $quality;
		return $this;
	}

	// -----------------

	public function resize(int $width, int|null $height = null): static|false
	{
		$result = imagescale($this->data, $width, $height ?? -1);

		if ($result instanceof GdImage) {
			$this->data = $result;
			return $this;
		}

		return false;
	}

	public function crop(int $width, int|null $height = null, int|null $x = null, int|null $y = null): static|false
	{
		$x = $x ?? max(0, ($this->properties->width / 2) - ($width / 2));
		$y = $y ?? max(0, ($this->properties->height / 2) - ($height / 2));

		$width = min($width, $this->properties->width);
		$height = min($height, $this->properties->height);

		$result = imagecrop($this->data, [
			'x' => (int)$x, 'y' => (int)$y, 'width' => $width, 'height' => $height,
		]);

		if ($result instanceof GdImage) {
			$this->data = $result;
			return $this;
		}

		return false;
	}

	// -----------------

	public function filter(int $type, array|int|float|bool|null ...$args): static|false
	{
		if (imagefilter($this->data, $type, ...$args)) {
			return $this;
		}

		return false;
	}

}