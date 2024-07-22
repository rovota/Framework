<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use JsonSerializable;
use Rovota\Framework\Routing\UrlObject;
use Stringable;

final class QrCode implements Stringable, JsonSerializable
{

	protected string $data;

	protected int $height = 200;
	protected int $width = 200;
	protected int $padding = 4;

	protected array $colors = [
		'background' => 'FFFFFF',
		'foreground' => '000000',
	];
	protected string $format = 'svg';

	// -----------------

	public function __construct(string $data)
	{
		$this->data = trim($data);
	}

	public function __toString(): string
	{
		return $this->url();
	}

	public function jsonSerialize(): string
	{
		return $this->url();
	}

	// -----------------

	public static function from(string $data): self
	{
		return new self($data);
	}

	// -----------------

	public function size(int $height, int $width): QrCode
	{
		$this->height = $height;
		$this->width = $width;
		return $this;
	}

	public function padding(int $padding): QrCode
	{
		$this->padding = limit(abs($padding), 0, 100);
		return $this;
	}

	public function colors(string $foreground, string|null $background = null): QrCode
	{
		if ($background !== null) {
			$this->colors['background'] = trim($background, '#');
		}
		$this->colors['foreground'] = trim($foreground, '#');
		return $this;
	}

	// -----------------

	public function url(): UrlObject
	{
		return Url::foreign('api.qrserver.com/v1/create-qr-code')->setParameters([
			'size' => $this->height.'x'.$this->width,
			'bgcolor' => $this->colors['background'],
			'color' => $this->colors['foreground'],
			'qzone' => $this->padding,
			'format' => $this->format,
			'data' => $this->data,
		]);
	}

}