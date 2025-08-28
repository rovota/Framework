<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Errors\Access;

use Rovota\Framework\Http\Errors\Error;

class MissingEndpointAccess extends Error
{

	public int $code = 201;

	public string $message = 'The token provided does not have access to this endpoint.';

}