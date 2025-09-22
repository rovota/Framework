<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Standard;

use Closure;
use Rovota\Framework\Validation\Rules\Rule;
use Rovota\Framework\Validation\ValidationTools;

class MaxRule extends Rule
{

	protected float|int $target = 0;

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		$actual = ValidationTools::getSize($value);

		if ($actual > $this->target) {
			$fail('The value must be at most :target.', data: [
				'actual' => $actual,
				'target' => $this->target,
			]);
		}
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (isset($options[0])) {
			$this->target = $options[0];
		}

		return $this;
	}

}