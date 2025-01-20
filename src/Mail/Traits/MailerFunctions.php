<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Traits;

use Rovota\Framework\Mail\Mailable;

trait MailerFunctions
{

	public function make(): Mailable
	{
		return new Mailable($this->name);
	}

	// -----------------

	public function attachHeader(string $name, string $value): void
	{
		$this->handler->addHeader($name, $value);
	}

	public function attachHeaders(array $headers): void
	{
		foreach ($headers as $name => $value) {
			$this->handler->addHeader($name, $value);
		}
	}

	// -----------------

	public function clear(): void
	{
		$this->handler->clear();
	}

	public function reset(): void
	{
		$this->handler->reset();
	}

}