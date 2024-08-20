<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Responses;

use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Response;
use Rovota\Framework\Support\Config;

class StatusResponse extends Response
{

	public function __construct(StatusCode|int|null $content, StatusCode|int $status, Config $config)
	{
		parent::__construct($content, $status, $config);
	}

	// -----------------

	protected function getPrintableContent(): string|null
	{
		return null;
	}

}