<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use Stringable;

class Message implements Stringable
{

	public string $name;

	public string $message;

	public array $data = [];

	// -----------------

	public function __construct(string $name, string $message, array $data = [])
	{
		$this->name = $name;
		$this->message = $message;

		if (empty($data) === false) {
			$this->data = array_merge_recursive($this->data, $data);
		}
	}

	// -----------------

	public function __toString(): string
	{
		return $this->formatted();
	}

	// -----------------

	public function formatted(): string
	{
		return Str::translate($this->message, $this->data);
	}

}