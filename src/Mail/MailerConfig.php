<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail;

use Rovota\Framework\Mail\Enums\Driver;
use Rovota\Framework\Support\Config;

/**
 * @property Driver|null $driver
 * @property string $label
 * @property Config $parameters
 */
final class MailerConfig extends Config
{

	protected function getDriver(): Driver|null
	{
		return Driver::tryFrom($this->string('driver'));
	}

	protected function getLabel(): string
	{
		return $this->string('label', 'Unnamed Transporter');
	}

	// -----------------

	protected function getParameters(): Config
	{
		return new Config($this->array('parameters'));
	}

	// -----------------

	public function isValid(): bool
	{
		return true;
	}

}