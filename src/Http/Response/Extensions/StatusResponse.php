<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Response\Extensions;

use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Response\DefaultResponse;
use Rovota\Framework\Support\Config;

class StatusResponse extends DefaultResponse
{

	public function __construct(StatusCode|int|null $content, StatusCode|int $status, Config $config)
	{
		parent::__construct($content, $status, $config);
	}

	// -----------------

	protected function render(): string|null
	{
		return null;
	}

}