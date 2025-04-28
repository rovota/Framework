<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules\Typing;

use Closure;
use Rovota\Framework\Http\Request\UploadedFile;
use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Validation\Rules\Rule;

class FileRule extends Rule
{

	public function validate(mixed $value, Closure $fail): void
	{
		if ($value instanceof UploadedFile) {
			$value = $value->source;
		}

		if ($value instanceof File === false) {
			$fail('The value must be a valid file.');
		}
	}

}