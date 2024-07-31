<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client\Integrations;

use Rovota\Framework\Http\Client\Client;
use Rovota\Framework\Http\Client\Integrations\Traits\HibpPasswordService;

class HibpClient extends Client
{
	use HibpPasswordService;

	// -----------------

	protected string|null $key = null;

	// -----------------

	public function __construct(string|null $key = null, mixed $options = [])
	{
		if ($key !== null) {
			$this->key = $key;
		}

		parent::__construct($options);
	}

}