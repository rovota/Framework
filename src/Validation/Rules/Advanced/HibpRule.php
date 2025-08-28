<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Advanced;

use Closure;
use Rovota\Framework\Http\Client\Integrations\BreachedPasswords;
use Rovota\Framework\Validation\Rules\Rule;

class HibpRule extends Rule
{

	protected int $threshold = 0;

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if (!is_string($value)) {
			$value = (string)$value;
		}

		$hibp = new BreachedPasswords();
		$matches = $hibp->appearances(sha1($value));

		if ($matches > $this->threshold) {
			$fail('The value has appeared in a data breach :count time(s) and should not be used.', data: [
				'count' => $matches,
				'threshold' => $this->threshold,
			]);
		}
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (isset($options[0])) {
			$this->threshold = $options[0];
		}

		return $this;
	}

}