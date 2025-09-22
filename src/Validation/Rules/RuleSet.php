<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules;

use Closure;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\MessageBag;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Traits\Errors;
use Rovota\Framework\Validation\Interfaces\ContextAware;
use Rovota\Framework\Validation\Interfaces\LastRuleIfNoErrors;

class RuleSet
{
	use Errors;

	// -----------------

	protected string $attribute;

	protected Bucket $data;

	// -----------------

	/**
	 * @var array<string, Rule>
	 */
	protected array $rules = [];

	protected array $flags = [];

	// -----------------

	public function __construct(string $attribute)
	{
		$this->errors = new MessageBag();
		$this->error_messages = new Bucket();

		$this->attribute = $attribute;
		$this->data = new Bucket();
	}

	// -----------------

	public static function build(string $attribute, array $rules): static
	{
		return new static($attribute)->withRules($rules);
	}

	// -----------------

	public function withRules(array $rules): static
	{
		foreach ($rules as $rule => $options) {
			if (is_int($rule)) {
				if ($options instanceof Closure) {
					$this->rules[Str::random(12)] = $options;
					continue;
				}
				if ($options instanceof Rule) {
					$this->attachRule($options);
					continue;
				}
				if (is_string($options)) {
					if (method_exists($this, $options)) {
						$this->{$options}();
						continue;
					}
					$this->attachRule($options);
					continue;
				}
			}

			$this->attachRule($rule, $options);
		}

		return $this;
	}

	public function withData(mixed $data): static
	{
		$this->data = $data;
		return $this;
	}

	// -----------------

	public function exclude(array|string $items): static
	{
		$items = is_array($items) ? $items : [$items];

		foreach ($items as $item) {
			if (isset($this->rules[$item])) {
				unset($this->rules[$item]);
			}
		}
		return $this;
	}

	// -----------------

	public function validate(): void
	{
		if ($this->data->isEmpty() || in_array('sometimes', $this->flags) && $this->data->missing($this->attribute)) {
			return;
		}

		if (in_array('required', $this->flags) && $this->data->missing($this->attribute)) {
			$this->addError($this->attribute, 'required', 'This attribute is required.');
			return;
		}

		if (in_array('nullable', $this->flags) && $this->data->get($this->attribute) === null) {
			return;
		}

		foreach ($this->rules as $name => $rule) {
			if (in_array('bail', $this->flags) && $this->errors->isEmpty() === false) {
				return;
			}

			$value = $this->data->get($this->attribute);

			if ($rule instanceof Closure) {
				$rule($value, $this->failureCallback($name));
			} else {
				if ($rule instanceof ContextAware) {
					$rule->context->import($this->data);
				}
				$rule->validate($value, $this->failureCallback($rule->name));

				if ($rule instanceof LastRuleIfNoErrors && $this->errors->isEmpty()) {
					return;
				}
			}
		}
	}

	// -----------------

	protected function required(): void
	{
		$this->flags[] = 'required';
	}

	protected function sometimes(): void
	{
		$this->flags[] = 'sometimes';
	}

	protected function nullable(): void
	{
		$this->flags[] = 'nullable';
	}

	protected function bail(): void
	{
		$this->flags[] = 'bail';
	}

	// -----------------

	protected function failureCallback(string $identifier): Closure
	{
		return function (string $message, array $data = []) use ($identifier) {
			$this->addError($this->attribute, $identifier, $message, array_merge($data, [
				'attribute' => $this->attribute,
			]));
		};
	}

	// -----------------

	protected function attachRule(Rule|string $rule, mixed $options = []): void
	{
		if (is_string($rule)) {
			$rule = RuleManager::get($rule)->withOptions(Arr::from($options));
		}

		$this->rules[$rule->name] = $rule;
	}

	// -----------------

	public function addError(string $type, string $identifier, string $message, array $data = []): void
	{
		if ($this->error_messages->has($identifier)) {
			$message = $this->error_messages->string($identifier);
		}

		$this->errors->set($identifier, $message, $data);
	}

}