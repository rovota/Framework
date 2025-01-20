<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail;

use Rovota\Framework\Mail\Interfaces\MailerInterface;
use Rovota\Framework\Mail\Interfaces\MailHandlerInterface;
use Rovota\Framework\Mail\Traits\MailerFunctions;

abstract class Mailer implements MailerInterface
{
	use MailerFunctions;

	// -----------------

	public string $name {
		get => $this->name;
	}

	public MailerConfig $config {
		get => $this->config;
	}

	public MailHandlerInterface $handler {
		get => $this->handler;
	}

	// -----------------

	public function __construct(string $name, MailHandlerInterface $handler, MailerConfig $config)
	{
		$this->name = $name;
		$this->config = $config;
		$this->handler = $handler;
	}

	// -----------------

	public function __toString(): string
	{
		return $this->name;
	}

	// -----------------

	public function isDefault(): bool
	{
		return MailManager::instance()->getDefault() === $this->name;
	}

}