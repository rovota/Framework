<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Handlers;

use PHPMailer\PHPMailer\PHPMailer;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Mail\Enums\Priority;
use Rovota\Framework\Mail\Interfaces\MailHandlerInterface;
use Rovota\Framework\Support\Config;
use Throwable;

class SmtpHandler implements MailHandlerInterface
{

	protected Config $parameters;

	protected PHPMailer $mailer;

	// -----------------

	public function __construct(Config $parameters)
	{
		$this->parameters = $parameters;

		$this->mailer = $this->getInstance($this->parameters);
	}

	// -----------------

	public function addRecipient(string $address, string|null $name = null): bool
	{
		try {
			$this->mailer->addAddress($address, $name ?? '');
		} catch (Throwable $throwable) {
			ExceptionHandler::handleThrowable($throwable);
			return false;
		}
		return true;
	}

	// -----------------

	public function setFrom(string $address, string|null $name = null): bool
	{
		try {
			$this->mailer->setFrom($address, $name ?? '');
		} catch (Throwable $throwable) {
			ExceptionHandler::handleThrowable($throwable);
			return false;
		}
		return true;
	}

	public function setReplyTo(string $address, string|null $name = null): bool
	{
		try {
			$this->mailer->addReplyTo($address, $name ?? '');
		} catch (Throwable $throwable) {
			ExceptionHandler::handleThrowable($throwable);
			return false;
		}
		return true;
	}

	// -----------------

	public function setPriority(Priority|int $level): void
	{
		$this->mailer->Priority = Priority::tryFrom($level) ?? Priority::Normal;
	}

	// -----------------

	public function setSubject(string $subject): void
	{
		$this->mailer->Subject = mb_trim($subject);
	}

	public function setPlainText(string $content): void
	{
		$this->mailer->AltBody = mb_trim($content);
	}

	public function setHtml(string $content): void
	{
		$this->mailer->isHTML();
		$this->mailer->Body = $content;
	}

	// -----------------

	public function addHeader(string $name, string $value): bool
	{
		try {
			$this->mailer->addCustomHeader($name, $value);
		} catch (Throwable $throwable) {
			ExceptionHandler::handleThrowable($throwable);
			return false;
		}
		return true;
	}

	public function removeHeader(string $name): void
	{
		$this->mailer->clearCustomHeader($name);
	}

	// -----------------

	// TODO: Attachments

	// -----------------

	public function send(): bool
	{
		try {
			$this->mailer->send();
		} catch (Throwable $throwable) {
			ExceptionHandler::handleThrowable($throwable);
			return false;
		}

		$this->clear();
		return true;
	}

	// -----------------

	public function clear(): void
	{
		$this->mailer->clearAllRecipients();
		$this->mailer->clearAttachments();
	}

	public function reset(): void
	{
		$this->mailer = $this->getInstance($this->parameters);
	}

	// -----------------

	protected function getInstance(Config $parameters): PHPMailer
	{
		$instance = new PHPMailer();
		$instance->isSMTP();
		$instance->CharSet = PHPMailer::CHARSET_UTF8;
		$instance->SMTPAuth = true;

		$instance->Host = $parameters->string('host');
		$instance->Port = $parameters->int('port');
		$instance->Username = $parameters->string('user');
		$instance->Password = $parameters->string('password');

		if ($parameters->has('program_name')) {
			$instance->XMailer = $parameters->string('program_name');
		}

		try {
			if ($parameters->has('from')) {
				$instance->setFrom(...$parameters->array('from'));
			}
			if ($parameters->has('reply_to')) {
				$instance->addReplyTo(...$parameters->array('reply_to'));
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::handleThrowable($throwable);
		}

		return $instance;
	}

}