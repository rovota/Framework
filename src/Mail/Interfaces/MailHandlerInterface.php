<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Interfaces;

use Rovota\Framework\Mail\Enums\Priority;

interface MailHandlerInterface
{

	public function addRecipient(string $name, string $address): bool;

	// -----------------

	public function setFrom(string $name, string $address): bool;

	public function setReplyTo(string $name, string $address): bool;

	// -----------------

	public function setPriority(Priority|int $level): void;

	// -----------------

	public function setSubject(string $subject): void;

	public function setPlainText(string $content): void;

	public function setHtml(string $content): void;

	// -----------------

	public function addHeader(string $name, string $value): bool;

	public function removeHeader(string $name): void;

	// -----------------

	// TODO: Attachments

	// -----------------

	public function send(): bool;

	// -----------------

	public function clear(): void;

	public function reset(): void;

}