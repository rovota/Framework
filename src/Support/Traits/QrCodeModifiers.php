<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Support\Traits;

use Rovota\Framework\Support\QrCode;

trait QrCodeModifiers
{

	public function withMargin(int $margin): QrCode
	{
		$this->config->margin = $margin;
		return $this;
	}

	// -----------------

	public function withSize(int $height, int $width): QrCode
	{
		$this->config->height = $height;
		$this->config->width = $width;
		return $this;
	}

	// -----------------

	public function withBackground(string $color): QrCode
	{
		$this->config->background = $color;
		return $this;
	}

	public function withForeground(string $color): QrCode
	{
		$this->config->foreground = $color;
		return $this;
	}

}