<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Advanced;

use Closure;
use Rovota\Framework\Validation\Rules\Rule;
use Rovota\Framework\Validation\ValidationTools;

class ExistsRule extends Rule
{

	protected array $config = [];

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if (!is_string($value) && !is_int($value)) {
			$value = (string)$value;
		}

		$config = ValidationTools::processDatabaseOptions($this->config);
		$occurrences = ValidationTools::getOccurrences($config, $value);

		if ($occurrences === 0) {
			$fail('There are no matching results for :value.', data: [
				'value' => $value,
			]);
		}
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->config = $options;
		}

		return $this;
	}

}