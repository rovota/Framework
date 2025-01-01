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

	protected string $name;

	protected MailerConfig $config;

	protected MailHandlerInterface $handler;

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

	// -----------------

	public function getName(): string
	{
		return $this->name;
	}

	public function getConfig(): MailerConfig
	{
		return $this->config;
	}

	public function getHandler(): MailHandlerInterface
	{
		return $this->handler;
	}

}