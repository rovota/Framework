<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Advanced;

use Closure;
use Rovota\Framework\Validation\Rules\Rule;

class HashRule extends Rule
{

	protected string $algorithm = 'sha1';

	protected string $reference = '-';

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		$hash = hash($this->algorithm, $this->reference);

		if ($value !== $hash) {
			$fail('The provided hash is incorrect.', data: [
				'algorithm' => $this->algorithm,
				'reference' => $this->reference,
				'hash' => $hash,
			]);
		}
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->algorithm = $options[0];
			$this->reference = $options[1];
		}

		return $this;
	}

}