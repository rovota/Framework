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
use Rovota\Framework\Validation\ValidationTools;

class MimesRule extends Rule
{

	protected array $extensions = [];

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if ($value instanceof UploadedFile) {
			$value = $value->source;
		}

		if (!$value instanceof File) {
			return;
		}

		foreach ($this->extensions as $extension) {
			$mime_types = ValidationTools::extensionMimeTypes($extension);
			if (empty($mime_types)) {
				continue;
			}
			if ($value->isAnyMimeType($mime_types)) {
				return;
			}
		}

		$fail('The value must be of an allowed type.', data: [
			'allowed' => $this->extensions,
		]);
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