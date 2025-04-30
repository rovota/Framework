<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Traits;

use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\MessageBag;
use Rovota\Framework\Validation\Validator;

trait Errors
{

	public MessageBag $errors;

	protected Bucket $error_messages;

	// -----------------

	public function addError(string $type, string $identifier, string $message, array $data = []): void
	{
		if ($this->error_messages->has($type)) {
			$message = $this->error_messages->array($type)[$identifier];
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

	public function withErrorMessages(array $messages): static
	{
		$this->error_messages->import($messages);
		return $this;
	}

	// -----------------

	public function clearErrors(): void
	{
		$this->errors->flush();
	}

	public function clearErrorMessages(): void
	{
		$this->error_messages->flush();
	}

}