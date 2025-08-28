<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

final class JsonRequest extends BasicRequest implements HasBody
{
	use HasJsonBody;

	// -----------------

	public function with(array $data): JsonRequest
	{
		$this->body()->merge($data);
		return $this;
	}

}