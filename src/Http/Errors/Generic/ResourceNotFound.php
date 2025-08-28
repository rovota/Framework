<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Errors\Generic;

use Rovota\Framework\Http\Errors\Error;

class ResourceNotFound extends Error
{

	public int $code = 300;

	public string $message = 'No resource could be found matching the provided parameters.';

}