<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Rovota\Framework\Structures\Config;

/**
 * @property string $data
 * @property string $format
 * @property int $margin
 * @property int $height
 * @property int $width
 * @property-read string $size
 * @property string $background
 * @property string $foreground
 */
class QrCodeConfig extends Config
{

	protected function getData(): string
	{
		return $this->get('data', '-');
	}

	protected function getFormat(): string
	{
		// PNG, GIF, JPEG, SVG, EPS
		return $this->get('format', 'svg');
	}

	// -----------------

	protected function getMargin(): int
	{
		return $this->int('margin', 4);
	}

	// -----------------

	protected function getHeight(): int
	{
		return $this->int('height', 200);
	}

	protected function getWidth(): int
	{
		return $this->int('width', 200);
	}

	protected function getSize(): string
	{
		return $this->getHeight().'x'.$this->getWidth();
	}

	// -----------------

	protected function getBackground(): string
	{
		return $this->text('background', new Text('FFFFFF'))->remove('#');
	}

	protected function getForeground(): string
	{
		return $this->text('foreground', new Text('000000'))->remove('#');
	}

}