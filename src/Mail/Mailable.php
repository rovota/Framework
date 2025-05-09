<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail;

use Rovota\Framework\Mail\Enums\Priority;
use Rovota\Framework\Mail\Events\MailableDelivered;
use Rovota\Framework\Mail\Interfaces\MailerInterface;
use Rovota\Framework\Mail\Traits\MailableContent;
use Rovota\Framework\Mail\Traits\MailableEvents;

class Mailable
{
	use MailableContent, MailableEvents;

	// -----------------

	protected MailerInterface $mailer;

	public array $attributes = [];

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

	public function to(mixed $address, string|null $name = null): static
	{
		$entity = MailManager::getNormalizedEntity($address, $name);

		$this->with('mail_receiver_name', $entity->name);
		$this->with('mail_receiver_address', $entity->address);

		$this->attributes['to'] = [
			'name' => $entity->name,
			'address' => $entity->address,
		];

		$this->mailer->handler->addRecipient($entity->address, $entity->name);
		return $this;
	}

	// -----------------

	public function from(mixed $address, string|null $name = null): static
	{
		$entity = MailManager::getNormalizedEntity($address, $name);

		$this->mailer->handler->setFrom($entity->name, $entity->address);
		return $this;
	}

	public function replyTo(mixed $address, string|null $name = null): static
	{
		$entity = MailManager::getNormalizedEntity($address, $name);

		$this->mailer->handler->setReplyTo($entity->name, $entity->address);
		return $this;
	}

	// -----------------

	public function priority(Priority|int $level): static
	{
		$this->mailer->handler->setPriority($level);
		return $this;
	}

	// -----------------

	public function subject(string $subject): static
	{
		$this->attributes['subject'] = trim($subject);

		$this->mailer->handler->setSubject($subject);
		return $this;
	}

	public function text(string $text): static
	{
		$this->mailer->handler->setPlainText($text);
		return $this;
	}

	public function html(string $data): static
	{
		$this->mailer->handler->setHtml($data);
		return $this;
	}

	// -----------------

	public function withHeader(string $name, string $value): static
	{
		$this->mailer->handler->addHeader($name, $value);
		return $this;
	}

	public function withHeaders(array $headers): static
	{
		foreach ($headers as $name => $value) {
			$this->mailer->handler->addHeader($name, $value);
		}
		return $this;
	}

	public function withoutHeader(string $name): static
	{
		$this->mailer->handler->removeHeader($name);
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

	public function deliver(): bool
	{
		$content = $this->render();

		if ($content !== null) {
			$this->html($content);
		}

		if ($this->mailer->handler->send()) {
			MailableDelivered::dispatch($this);
			return true;
		}

		return false;
	}

}