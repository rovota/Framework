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
		$this->getHandler()->addHeader($name, $value);
	}

	public function attachHeaders(array $headers): void
	{
		foreach ($headers as $name => $value) {
			$this->getHandler()->addHeader($name, $value);
		}
	}

	// -----------------

	public function clear(): void
	{
		$this->getHandler()->clear();
	}

	public function reset(): void
	{
		$this->getHandler()->reset();
	}

}