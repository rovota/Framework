<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Interfaces;

use Rovota\Framework\Mail\Mailable;
use Rovota\Framework\Mail\MailerConfig;

interface MailerInterface
{

	public function __toString(): string;

	// -----------------

	public function isDefault(): bool;

	// -----------------

	public function getName(): string;

	public function getConfig(): MailerConfig;

	public function getHandler(): MailHandlerInterface;

	// -----------------

	public function make(): Mailable;

	// -----------------

	public function attachHeader(string $name, string $value): void;

	public function attachHeaders(array $headers): void;

	// -----------------

	public function clear(): void;

	public function reset(): void;

}