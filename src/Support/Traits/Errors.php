<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Traits;

use Rovota\Framework\Support\MessageBag;
use Rovota\Framework\Validation\Validator;

trait Errors
{

	public MessageBag $errors;

	protected array $message_overrides = [];

	// -----------------

	public function addError(string $type, string $identifier, string $message, array $data = []): void
	{
		if (isset($this->message_overrides[$type][$identifier])) {
			$message = $this->message_overrides[$type][$identifier];
		}

		$this->errors->set($type.'.'.$identifier, $message, $data);
	}

	// -----------------

	public function withErrors(Validator|MessageBag|array $errors): static
	{
		if ($errors instanceof Validator) {
			$errors = $errors->errors;
		}
		$this->errors->import($errors);
		return $this;
	}

	// -----------------

	public function setErrorMessage(string $type, string $identifier, string $message): static
	{
		$this->message_overrides[$type][$identifier] = trim($message);
		return $this;
	}

	public function setErrorMessages(array $messages): static
	{
		$this->message_overrides = array_replace_recursive($this->message_overrides, $messages);
		return $this;
	}

	// -----------------

	public function clearErrors(): void
	{
		$this->errors->flush();
	}

	public function clearErrorMessages(): void
	{
		$this->message_overrides = [];
	}

}