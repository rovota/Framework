<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Http\Client\Traits;

use Psr\Http\Message\StreamInterface;

trait ResponseContent
{

	public function body(): StreamInterface
	{
		return $this->response->getBody();
	}

	public function metadata(): array
	{
		return $this->body()->getMetadata();
	}

	public function size(): int|null
	{
		return $this->body()->getSize();
	}

	// -----------------

	public function content(): string
	{
		return $this->body()->getContents();
	}

	// -----------------

	public function json(): mixed
	{
		$contents = $this->content();
		if (json_validate($contents)) {
			return json_decode($contents);
		}
		return null;
	}

	public function jsonAsArray(): array
	{
		$content = $this->json();
		if ($content !== null) {
			return is_array($content) ? $content : [$content];
		}
		return [];
	}

}