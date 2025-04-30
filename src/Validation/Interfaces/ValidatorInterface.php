<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Interfaces;

use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\MessageBag;

interface ValidatorInterface
{

	public MessageBag $errors {
		get; set;
	}

	// -----------------

	public static function create(mixed $data, array $rules, array $messages = []): static;

	// -----------------

	public function validate(): void;

	public function clear(): static;

	public function safe(): Bucket;

	// -----------------

	public function succeeds(): bool;

	public function fails(): bool;

}