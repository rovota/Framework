<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Storage;

use Closure;
use Rovota\Framework\Http\Request\UploadedFile;
use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Validation\Rules\Rule;

class ExtensionsRule extends Rule
{

	protected array $extensions = [];

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if ($value instanceof UploadedFile) {
			$value = $value->source;
		}

		if ($value instanceof File && $value->isAnyExtension($this->extensions) === false) {
			$fail('The file must have one of the allowed extensions.', data: [
				'allowed' => $this->extensions,
			]);
		}
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->extensions = $options;
		}

		return $this;
	}

}