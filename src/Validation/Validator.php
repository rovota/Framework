<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation;

use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\MessageBag;
use Rovota\Framework\Support\Traits\Errors;
use Rovota\Framework\Validation\Rules\RuleSet;

class Validator
{
	use Errors;

	// -----------------

	public Bucket $unsafe;
	public Bucket $safe;

	/**
	 * @var array<string, RuleSet>
	 */
	protected array $rules = [];

	// -----------------

	public function __construct(mixed $data, array $rules = [], array $messages = [])
	{
		$this->errors = new MessageBag();
		$this->error_messages = new Bucket();

		$this->unsafe = new Bucket($data);
		$this->safe = new Bucket();

		$this->configureRuleSets($rules);
		$this->configureMessages($messages);
	}

	// -----------------

	public static function create(mixed $data, array $rules = [], array $messages = []): static
	{
		return new static($data, $rules, $messages);
	}

	// -----------------

	protected function rules(): array
	{
		return [];
	}

	protected function messages(): array
	{
		return [];
	}

	// -----------------

	protected function beforeValidation(): void
	{

	}

	protected function afterValidation(): void
	{

	}

	// -----------------

	public function exclude(array|string $items): static
	{
		$items = is_array($items) ? $items : [$items];

		foreach ($items as $item) {
			if (str_starts_with($item, '*.')) {
				foreach ($this->rules as $rule) {
					$rule->exclude(substr($item, 2));
				}
				continue;
			}
			if (str_contains($item, '.')) {
				foreach ($this->rules as $attribute => $rule) {
					if (str_starts_with($attribute, $item)) {
						$rule->exclude(substr($attribute, strlen($item) + 1));
					}
				}
			}

			if (isset($this->rules[$item])) {
				unset($this->rules[$item]);
			}
		}

		return $this;
	}

	// -----------------

	public function validate(): void
	{
		$this->beforeValidation();

		foreach ($this->rules as $attribute => $set) {
			$set->withData($this->unsafe)->validate();

			if ($set->errors->count() > 0) {
				foreach ($set->errors as $identifier => $error) {
					$this->errors->set($attribute . '.' . $identifier, $error);
				}
			} else {
				$this->safe->set($attribute, $this->unsafe->get($attribute));
			}
		}

		$this->afterValidation();
	}

	public function clear(): static
	{
		$this->clearErrors();
		$this->clearErrorMessages();

		$this->unsafe->flush();
		$this->safe->flush();
		$this->rules = [];

		return $this;
	}

	// -----------------

	public function succeeds(): bool
	{
		$this->validate();

		return $this->errors->isEmpty();
	}

	public function fails(): bool
	{
		return $this->succeeds() === false;
	}

	// -----------------

	private function configureRuleSets(array $entries): void
	{
		$entries = array_replace_recursive($this->rules(), $entries);

		foreach ($entries as $attribute => $rules) {
			$this->rules[$attribute] = RuleSet::build($attribute, $rules);
		}
	}

	private function configureMessages(array $entries): void
	{
		if (empty($entries) === false || empty($this->messages()) === false) {
			$entries = array_replace_recursive($this->messages(), $entries);

			foreach ($entries as $attribute => $messages) {
				if (is_array($messages) === false) {
					foreach ($this->rules as $rule) {
						$rule->withErrorMessages([$attribute => $messages]);
					}
					continue;
				}

				if (isset($this->rules[$attribute])) {
					$this->rules[$attribute]->withErrorMessages($messages);
				}
			}
		}
	}

}