<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

class ApiError
{

	protected int $code;

	protected string $message;

	protected array $parameters = [];

	// -----------------

	public function __construct(string|null $message = null, array $parameters = [], int $code = 0)
	{
		$this->message = $message ?? 'There is no information available about this error.';
		$this->parameters = $parameters;
		$this->code = $code;
	}

	// -----------------

	public function getCode(): int
	{
		return $this->code;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	public function getParameters(): array
	{
		return $this->parameters;
	}

}