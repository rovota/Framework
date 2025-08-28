<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Errors\Access;

use Rovota\Framework\Http\Errors\Error;

class MissingResourceAccess extends Error
{

	public int $code = 202;

	public string $message = 'The token provided does not have access to this resource.';

}