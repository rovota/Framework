<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasFormBody;

final class FormRequest extends BasicRequest implements HasBody
{
	use HasFormBody;

	// -----------------

	public function with(array $data): FormRequest
	{
		$this->body()->merge($data);
		return $this;
	}
}