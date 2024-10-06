<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Errors\Access;

use Rovota\Framework\Http\Error;

class InvalidTokenProvided extends Error
{

	protected int $code = 200;

	protected string $message = 'A valid token must be provided before accessing this endpoint.';

}