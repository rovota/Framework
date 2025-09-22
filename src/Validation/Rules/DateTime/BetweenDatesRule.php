<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\DateTime;

use Closure;
use Rovota\Framework\Validation\Rules\Rule;

class BetweenDatesRule extends Rule
{

	protected mixed $start = 'now';

	protected mixed $end = 'now';

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if (!moment($value)->isBetween($this->start, $this->end)) {
			$fail('The value must be within the specified window.', data: [
				'start' => moment($this->start),
				'end' => moment($this->end),
			]);
		}
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (count($options) === 2) {
			$this->start = $options[0];
			$this->end = $options[1];
		}

		return $this;
	}

}