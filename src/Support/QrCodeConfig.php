<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

/**
 * @property-read string $data
 * @property-read string $format
 * @property int $margin
 * @property int $height
 * @property int $width
 * @property-read string $size
 * @property string $background
 * @property string $foreground
 */
final class QrCodeConfig extends Config
{

	protected function getData(): string
	{
		return $this->string('data', '-');
	}

	protected function getFormat(): string
	{
		// PNG, GIF, JPEG, SVG, EPS
		return $this->string('format', 'svg');
	}

	// -----------------

	protected function getMargin(): int
	{
		return $this->int('margin', 4);
	}

	protected function setMargin(int $margin): void
	{
		$this->set('margin', limit(abs($margin), 0, 100));
	}

	// -----------------

	protected function getHeight(): int
	{
		return $this->int('height', 200);
	}

	protected function setHeight(int $height): void
	{
		$this->set('height', abs($height));
	}

	protected function getWidth(): int
	{
		return $this->int('width', 200);
	}

	protected function setWidth(int $width): void
	{
		$this->set('width', abs($width));
	}

	protected function getSize(): string
	{
		return $this->getHeight().'x'.$this->getWidth();
	}

	// -----------------

	protected function getBackground(): string
	{
		return $this->string('background', 'FFFFFF');
	}

	protected function setBackground(string $color): void
	{
		$this->set('background', Str::remove($color, '#'));
	}

	protected function getForeground(): string
	{
		return $this->string('foreground', '000000');
	}

	protected function setForeground(string $color): void
	{
		$this->set('foreground', Str::remove($color, '#'));
	}

}