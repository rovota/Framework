<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Errors\Access;

use Rovota\Framework\Http\Error;

class MissingResourceAccess extends Error
{

	protected int $code = 202;

	protected string $message = 'The token provided does not have access to this resource.';

}