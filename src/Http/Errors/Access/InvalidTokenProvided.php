<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Errors\Access;

use Rovota\Framework\Http\Error;

class InvalidTokenProvided extends Error
{

	public int $code = 200;

	public string $message = 'A valid token must be provided before accessing this endpoint.';

}