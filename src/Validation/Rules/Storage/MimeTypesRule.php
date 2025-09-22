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

class MimeTypesRule extends Rule
{

	protected array $mime_types = [];

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if ($value instanceof UploadedFile) {
			$value = $value->source;
		}

		if ($value instanceof File && $value->isAnyMimeType($this->mime_types) === false) {
			$fail('The value must be of an allowed type.', data: [
				'allowed' => $this->mime_types,
			]);
		}
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->mime_types = $options;
		}

		return $this;
	}

}