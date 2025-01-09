<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail;

use Rovota\Framework\Mail\Enums\Priority;
use Rovota\Framework\Mail\Interfaces\MailerInterface;
use Rovota\Framework\Mail\Traits\MailableContent;
use Rovota\Framework\Mail\Traits\MailableEvents;

class Mailable
{
	use MailableContent, MailableEvents;

	// -----------------

	protected MailerInterface $mailer;

	protected array $attributes = [];

	// -----------------

	public function __construct(MailerInterface|string|null $mailer = null)
	{
		$this->mailer = $mailer instanceof MailerInterface ? $mailer : MailManager::instance()->get($mailer);

		$this->configuration();
	}

	// -----------------

	protected function configuration(): void
	{

	}

	protected function use(MailerInterface|string $mailer): void
	{
		$this->mailer = $mailer instanceof MailerInterface ? $mailer : MailManager::instance()->get($mailer);
	}

	// -----------------

	public function to(mixed $name, string|null $address = null): static
	{
		$entity = MailManager::getNormalizedEntity($name, $address);

		$this->with('mail_receiver_name', $entity->name);
		$this->with('mail_receiver_address', $entity->address);

		$this->attributes['to'] = [
			'name' => $entity->name,
			'address' => $entity->address,
		];

		$this->mailer->getHandler()->addRecipient($entity->name, $entity->address);
		return $this;
	}

	// -----------------

	public function from(mixed $name, string|null $address = null): static
	{
		$entity = MailManager::getNormalizedEntity($name, $address);

		$this->mailer->getHandler()->setFrom($entity->name, $entity->address);
		return $this;
	}

	public function replyTo(mixed $name, string|null $address = null): static
	{
		$entity = MailManager::getNormalizedEntity($name, $address);

		$this->mailer->getHandler()->setReplyTo($entity->name, $entity->address);
		return $this;
	}

	// -----------------

	public function priority(Priority|int $level): static
	{
		$this->mailer->getHandler()->setPriority($level);
		return $this;
	}

	// -----------------

	public function subject(string $subject): static
	{
		$this->attributes['subject'] = trim($subject);

		$this->mailer->getHandler()->setSubject($subject);
		return $this;
	}

	public function text(string $text): static
	{
		$this->mailer->getHandler()->setPlainText($text);
		return $this;
	}

	public function html(string $data): static
	{
		$this->mailer->getHandler()->setHtml($data);
		return $this;
	}

	// -----------------

	public function withHeader(string $name, string $value): static
	{
		$this->mailer->getHandler()->addHeader($name, $value);
		return $this;
	}

	public function withHeaders(array $headers): static
	{
		foreach ($headers as $name => $value) {
			$this->mailer->getHandler()->addHeader($name, $value);
		}
		return $this;
	}

	public function withoutHeader(string $name): static
	{
		$this->mailer->getHandler()->removeHeader($name);
		return $this;
	}

	public function unsubscribe(string|null $email = null, string|null $url = null): static
	{
		if ($email !== null) {
			$email = sprintf('<mailto: %s>', $email);
		}
		if ($url !== null) {
			$url = sprintf('<%s>', $url);
		}

		$this->withHeader('List-Unsubscribe', implode(', ', [$email, $url]));
		return $this;
	}

	// -----------------

	// TODO: Attachments

	// -----------------

	public function getAttributes(): array
	{
		return $this->attributes;
	}

	// -----------------

	public function deliver(): bool
	{
		$content = $this->render();

		if ($content !== null) {
			$this->html($content);
		}

		if ($this->mailer->getHandler()->send()) {
			$this->eventMailableDelivered();
			return true;
		}

		return false;
	}

}