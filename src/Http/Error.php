<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

class Error
{

	public int $code {
		get => $this->code;
	}

	public string $message {
		get => $this->message;
	}

	public array $parameters = [] {
		get => $this->parameters;
	}

	// -----------------

	public function __construct(string|null $message = null, array $parameters = [], int $code = 0)
	{
		$this->message = $message ?? 'There is no information available about this error.';
		$this->parameters = $parameters;
		$this->code = $code;
	}

}